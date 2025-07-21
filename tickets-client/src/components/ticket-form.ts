import { api } from "../api";
import { showNotification } from "./notification.ts";

export function initTicketFormDialog(options: { onSuccess: () => void }) {
    console.log("initTicketFormDialog");
    const dialog = $("<div></div>").kendoDialog({
        width: "400px",
        title: "Novo Chamado",
        closable: true,
        modal: true,
        content: `
      <form id="ticket-form" class="spsace-y-4">
        <div>
          <label class="block font-medium mb-1">Título</label>
          <input type="text" name="title" class="w-full border-1 h-10 px-2 rounded-md" required />
        </div>
        <div>
          <label class="block font-medium mb-1">Descrição</label>
          <textarea name="description" class="w-full border-1 p-2 rounded-md" rows="3" required></textarea>
        </div>
      </form>
    `,
        actions: [
            {
                text: "Cancelar"
            },
            {
                text: "Criar",
                primary: true,
                action: () => {
                    const form = $("#ticket-form")[0] as HTMLFormElement;
                    const formData = Object.fromEntries(new FormData(form));
                    if (!formData.title || !formData.description) {
                        showNotification("Preencha todos os campos.", "error");
                        return false;
                    }

                    api.post("/tickets", formData)
                        .then(() => {
                            dialog?.close();
                            options.onSuccess();
                        })
                        .catch((error) => {
                            console.log(error);
                            showNotification("Erro ao criar chamado.", "error");
                        });

                    return false;
                }
            }
        ]
    }).data("kendoDialog");

    dialog?.open();
}
