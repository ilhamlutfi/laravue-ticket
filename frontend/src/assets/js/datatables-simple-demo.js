    // Simple-DataTables
    // https://github.com/fiduswriter/Simple-DataTables/wiki

    export function DatatableSimple() {
        const datatablesSimple = document.getElementById('datatablesSimple');
        if (datatablesSimple) {
            new simpleDatatables.DataTable(datatablesSimple);
        }
    }
