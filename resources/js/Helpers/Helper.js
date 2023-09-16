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
    }
}



