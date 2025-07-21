import { api } from "../api";
import { showNotification } from "./notification.ts";

export function initTaskFormDialog(options: {
    ticketId: number,
    onSuccess: () => void
}) {
    const dialog = $("<div></div>").kendoDialog({
        width: "400px",
        title: "Nova Tarefa",
        closable: true,
        modal: true,
        content: `
      <form id="task-form" class="space-y-4">
        <div>
          <label class="block font-medium mb-1">Descrição</label>
          <textarea name="description" class="w-full border-1 p-2 rounded-md" rows="3" required></textarea>
        </div>
        <div>
          <label class="block font-medium mb-1">Responsável</label>
          <input type="text" name="assignee_name" class="w-full border-1 px-2 h-10 rounded-md" required />
        </div>
      </form>
    `,
        actions: [
            { text: "Cancelar" },
            {
                text: "Criar",
                primary: true,
                action: () => {
                    const form = $("#task-form")[0] as HTMLFormElement;
                    const formData = Object.fromEntries(new FormData(form));
                    if (!formData.description || !formData.assignee_name) {
                        showNotification("Preencha todos os campos.", "error");
                        return false;
                    }

                    api.post("/tasks", {
                        ...formData,
                        ticket_id: options.ticketId
                    }).then(() => {
                        dialog?.close();
                        options.onSuccess();
                    }).catch(() => {
                        showNotification("Erro ao criar tarefa.", "error");
                    });

                    return false;
                }
            }
        ]
    }).data("kendoDialog");

    dialog?.open();
}
