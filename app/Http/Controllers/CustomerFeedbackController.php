<?php

namespace App\Http\Controllers;

use App\Mail\FeedbackNotification;
use App\Mail\FeedbackReplyNotification;
use App\Models\CustomerFeedback;
use App\Models\CustomerFeedbackReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use illuminate\Support\Str;

class CustomerFeedbackController extends Controller
{
    public function index(): \Inertia\Response
    {
        return Inertia::render('CustomerFeedback/Feedback');
    }

    public function replyTicket($feedback): \Inertia\Response
    {
        $getFeedback = CustomerFeedback::where("ticket_id", $feedback)->first();
        $ticketID = $getFeedback->ticket_id;
        return Inertia::render('CustomerFeedback/FeedbackReplyForm', compact('getFeedback', 'ticketID'));
    }

    public function allTicket(): \Inertia\Response|\Illuminate\Http\RedirectResponse
    {
        if (!auth()->user()->is_admin) return to_route('feedback.tickets.user');
        $all_tickets = CustomerFeedback::latest()->paginate(20);
        return Inertia::render('CustomerFeedback/Index', compact(
            'all_tickets',
        ));
    }

    public function allUserTicket(): \Inertia\Response
    {
        $all_tickets = CustomerFeedback::where('user_id', auth()->user()->id)->latest()->paginate(20);
        return Inertia::render('CustomerFeedback/UserFeedback', compact(
            'all_tickets',
        ));
    }

    public function showTicket($CustomerFeedback): \Inertia\Response
    {
        $customer_feedback = CustomerFeedback::with(['user', 'customer_feedback_reply'])->where("ticket_id", $CustomerFeedback)->first();
        $c_role = $customer_feedback->user->is_admin;
        $getReplies = $customer_feedback->customer_feedback_reply()->with('user')->get();
        return Inertia::render('CustomerFeedback/ShowFeedback', compact(
            'customer_feedback', 'c_role', 'getReplies'
        ));
    }

    public function storeForm(Request $request): \Illuminate\Http\RedirectResponse
    {
        $data = $request->validate(['feedback_message' => 'required|string', 'feedback_subject' => 'required|string', 'feedback_file' => 'nullable|max:10240']);
        $user = auth()->user();
        $data['feedback_name'] = $user->first_name . ' ' . $user->last_name;
        $data['feedback_email'] = $user->email;
        $data['feedback_phone'] = $user->phone;
        $data['submitted_on'] = date('Y-m-d H:i:s');
        $data['ticket_id'] = Str::random(10);
        $data['user_id'] = $user->id;

        if ($file = $request->file('feedback_file')) {
            $name = time() . '_' . $file->getClientOriginalName();
            $request->file('feedback_file')->storeAs('media/', $name, 'public');
            $data['feedback_file'] = $name;
        }

        $getData = CustomerFeedback::create($data);
        Mail::to(env('MAIL_ADMIN'))->send(new FeedbackNotification($getData));
        return to_route('feedback.tickets.user');
    }

    public function saveTicketReply(Request $request, $ticket): \Illuminate\Http\RedirectResponse
    {
        $data = $request->validate(['feedback_message' => 'required|string']);
        $user = auth()->user();
        $main_ticket = CustomerFeedback::where("ticket_id", $ticket)->first();
        $data['feedback_name'] = $user->first_name . ' ' . $user->last_name;
        $data['feedback_email'] = $user->email;
        $data['feedback_phone'] = $user->phone;
        $data['submitted_on'] = date('Y-m-d H:i:s');
        $data['status'] = 0;
        $data['user_id'] = $user->id;
        $data['customer_feedback_id'] = $main_ticket->id;

        $reply = CustomerFeedbackReply::create($data);
        if ($user->is_admin || !$user->hasPermissionTo('create-user')) {
            Mail::to($main_ticket->feedback_email)->send(new FeedbackReplyNotification($reply, $ticket));
        }

        if (!$user->is_admin && !$user->hasPermissionTo('create-user')) {
            Mail::to(env('MAIL_ADMIN'))->send(new FeedbackReplyNotification($reply, $ticket));
        }

        return to_route('feedback.ticket.show', $ticket);
    }

    public function markReplyAsread($ticketID, $reply_id): \Illuminate\Http\RedirectResponse
    {
        $findReply = CustomerFeedbackReply::where("customer_feedback_id", $ticketID)->where('id', $reply_id)->first();
        if ($findReply->status == 0) {
            $findReply->status = 1;
            $findReply->save();
        }
        $ticket = $findReply->customer_feedback->ticket_id;
        return to_route('feedback.ticket.show', $ticket);
    }


    public function getTicketmedia($CustomerFeedback): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $customer_feedback = CustomerFeedback::where("ticket_id", $CustomerFeedback)->first();
        $c_file = $customer_feedback->feedback_file;
        return Storage::download('media/' . $c_file);
    }

}
