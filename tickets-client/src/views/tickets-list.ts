import { showNotification } from "../components/notification";
import { initTicketFormDialog } from "../components/ticket-form";
import { api } from "../api";
import type Ticket from "../Interfaces/Ticket.ts";

export function renderTicketsList() {
    $("#app").html(`
    <div class="p-4 space-y-4">
      <div class="flex justify-between items-center">
        <h1 class="text-xl font-bold">Lista de Chamados</h1>
        <button id="new-ticket-btn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
          Novo Chamado
        </button>
      </div>
      <div id="ticket-grid"></div>
    </div>
  `);

    $("#ticket-grid").kendoGrid({
        dataSource: {
            transport: {
                read: {
                    url: "http://localhost:8080/tickets",
                    dataType: "json"
                }
            },
            schema: {
                data: "data",
            },
            pageSize: 10,
        },
        height: 550,
        sortable: true,
        pageable: true,
        columns: [
            { field: "id", title: "ID", width: "50px" },
            { field: "title", title: "Título" },
            {
                field: "status",
                title: "Status",
                template: (data: Ticket) => {
                    const ticketStatusTextRender = {
                        open: "Aberto",
                        in_progress: "Em andamento",
                        closed: "Fechado",
                    };

                    const ticketStatusStyle = {
                        open: "bg-zinc-500",
                        in_progress: "bg-blue-500",
                        closed: "bg-green-500",
                    };

                    return `<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${ticketStatusStyle[data.status]} text-white">${ticketStatusTextRender[data.status]}</span>`;
                }
            },
            {
                command: {
                    text: "Visualizar",
                    click: (e) => {
                        e.preventDefault();

                        const grid = $("#ticket-grid").data("kendoGrid");
                        const dataItem = grid.dataItem($(e.currentTarget)
                            .closest("tr"));

                        if (dataItem) {
                            window.location.hash = `#/ticket?id=${dataItem.id}`;
                        }
                    }
                },
                title: "Ações",
                width: "120px"
            }
        ]
    });

    $("#new-ticket-btn").on("click", () => {
        initTicketFormDialog({
            onSuccess: () => {
                showNotification("Chamado criado com sucesso!", "success");
                const grid = $("#ticket-grid").data("kendoGrid");
                grid.dataSource.read();
            }
        });
    });
}
