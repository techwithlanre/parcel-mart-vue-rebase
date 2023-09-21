import exportFromJSON from "export-from-json";

export default {
    nairaFormat(amount) {
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'NGN'
        }).format(amount);
    },

    exportDataFromJSON(data, newFileName, fileExportType) {
        if (!data) return;
        try {
            const fileName = newFileName || "exported-data";
            const exportType = exportFromJSON.types[fileExportType || "xls"];
            exportFromJSON({ data, fileName, exportType });
        } catch (e) {
            throw new Error("Parsing failed!");
        }
    },

    status(status) {
        let statusData = [
            {
                'processing': 'bg-blue-100 text-blue-800',
            },
            {
                'processing': 'bg-blue-100 text-blue-800',
            },
            {
                'processing': 'bg-blue-100 text-blue-800',
            },
        ];
        return '<span class="px-3 py-1 rounded-full text-white font-medium" :class="{\'\' : item.status ===\'processing\', \'bg-yellow-400\' : item.status ===\'pending\', \'bg-green-400\' : item.status ===\'delivered\'}">'
    }
}



