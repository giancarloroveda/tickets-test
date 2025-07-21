import { api } from "../api";
import { showNotification } from "../components/notification";
import { initTaskFormDialog } from "../components/task-form";
import type Ticket from "../Interfaces/Ticket.ts";

export async function renderTicketDetails(id: number) {
    try {
        const { data: ticket } = await api.get(`/tickets/${id}`);

        loadTicketData(ticket);

        $("#app").append(`
<div class="flex items-center justify-end p-4">
  <button id="delete-ticket" class="k-button k-button-md k-rounded-md k-button-solid k-button-solid-danger ml-2">
    Deletar Chamado
  </button>
  <button id="back-to-list" class="k-button k-button-md k-rounded-md k-button-solid k-button-solid-danger ml-2">
    ← Voltar para lista de chamados
  </button>
</div>
`);

        $("#back-to-list").on("click", () => {
            window.location.hash = "#/tickets";
        });


        $("#delete-ticket").on("click", async () => {
            if (!confirm("Tem certeza que deseja deletar este chamado?")) return;

            try {
                const res = await api.delete(`/tickets/${ticket.id}`);

                if (res.success) {
                    showNotification(
                        "Chamado deletado com sucesso!",
                        "success"
                    );
                    window.location.hash = "#/tickets";
                } else {
                    showNotification(
                        "Erro ao deletar chamado: " + res.message,
                        "error"
                    );
                }


            } catch (error) {
                showNotification("Erro ao deletar chamado.", "error");
            }
        });


        // Inicializa o dropdown de status
        $("#ticket-status").kendoDropDownList({
            change: async function () {
                const status = this.value();
                const payload = {
                    ...ticket,
                    status
                };
                await api.put(
                    `/tickets/${ticket.id}`,
                    payload
                ).then((res) => {
                    if (res.success) {
                        showNotification(
                            "Status do chamado atualizado com sucesso!",
                            "success"
                        );
                    } else {
                        showNotification(
                            "Erro ao atualizar status do chamado: " +
                            res.message,
                            "error"
                        );
                    }
                }).catch((error) => {
                    showNotification(
                        "Erro ao atualizar status do chamado!",
                        "error"
                    );
                });
            }
        });

        // Inicializa o botão para criar tarefa
        $("#new-task-btn").on("click", () => {
            initTaskFormDialog({
                ticketId: ticket.id,
                onSuccess: () => {
                    showNotification("Tarefa criada com sucesso!", "success");
                    const grid = $("#task-grid").data("kendoGrid");
                    grid.dataSource.read();
                }
            });
        });

        // Inicializa o grid de tarefas
        $("#task-grid").kendoGrid({
            dataSource: {
                transport: {
                    read: {
                        url: `http://localhost:8080/tasks/by-ticket/${ticket.id}`,
                        dataType: "json"
                    }
                },
                schema: {
                    data: "data"
                },
                pageSize: 5
            },
            pageable: true,
            editable: false,
            columns: [
                { field: "id", title: "ID", width: "60px" },
                { field: "description", title: "Descrição" },
                { field: "assignee_name", title: "Responsável" },
                {
                    field: "status",
                    title: "Status",
                    template: dataItem => `
            <select class="task-status-dropdown" data-id="${dataItem.id}">
              <option value="pending" ${dataItem.status === "pending"
                        ? "selected"
                        : ""}>Pendente</option>
              <option value="in_progress" ${dataItem.status === "in_progress"
                        ? "selected"
                        : ""}>Fazendo</option>
              <option value="done" ${dataItem.status === "done"
                        ? "selected"
                        : ""}>Concluída</option>
            </select>
          `,
                    width: "180px"
                },
                {
                    title: "Ações",
                    template: dataItem => `
      <button class="delete-task-btn k-button k-button-sm k-button-solid k-button-solid-danger" data-id="${dataItem.id}">
        Deletar
      </button>
    `,
                    width: "100px"
                }
            ],
            dataBound: function () {
                $(".task-status-dropdown").kendoDropDownList({
                    change: function () {
                        const taskId = $(this.element).data("id");
                        const newStatus = this.value();

                        const row = $(this.element).closest("tr");

                        const grid = $("#task-grid").data("kendoGrid");

                        const task = grid.dataItem(row);

                        const payload = {
                            description: task.description,
                            assignee_name: task.assignee_name,
                            status: newStatus
                        };

                        api.put(`/tasks/${taskId}`, payload)
                            .then(() => {
                                showNotification(
                                    "Status da tarefa atualizado!",
                                    "success"
                                );
                            })
                            .catch(() => {
                                showNotification(
                                    "Erro ao atualizar status da tarefa",
                                    "error"
                                );
                            });
                    }
                });

                $(".delete-task-btn").on("click", async function () {
                    const taskId = $(this).data("id");
                    if (!confirm("Tem certeza que deseja deletar esta tarefa?")) return;

                    try {
                        await api.delete(`/tasks/${taskId}`);
                        showNotification("Tarefa deletada com sucesso!", "success");
                        $("#task-grid").data("kendoGrid").dataSource.read();
                    } catch (error) {
                        showNotification("Erro ao deletar tarefa.", "error");
                    }
                });

            }
        });

    } catch (err) {
        $("#app").html("<p>Erro ao carregar o chamado.</p>");
    }
}

function loadTicketData(ticket: Ticket) {
    $("#app").html(`
      <div class="p-4 space-y-6">
        <div class="space-y-2">
          <h1 class="text-2xl font-bold">Chamado #${ticket.id}</h1>
          <p><strong>Título:</strong> ${ticket.title}</p>
          <p><strong>Descrição:</strong> ${ticket.description}</p>
          <label class="block font-medium">Status:</label>
          <select id="ticket-status" class="k-dropdown w-full max-w-xs">
            <option value="open" ${ticket.status === "open"
        ? "selected"
        : ""}>Aberto</option>
            <option value="in_progress" ${ticket.status === "in_progress"
        ? "selected"
        : ""}>Em Andamento</option>
            <option value="closed" ${ticket.status === "closed"
        ? "selected"
        : ""}>Finalizado</option>
          </select>
        </div>

        <div class="flex justify-between items-center mt-6">
          <h2 class="text-xl font-semibold">Tarefas</h2>
          <button id="new-task-btn" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Nova Tarefa</button>
        </div>

        <div id="task-grid"></div>
      </div>
    `);
}
