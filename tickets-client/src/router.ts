import { renderTicketsList } from "./views/tickets-list";
import { renderTicketDetails } from "./views/ticket-details";

export function handleRouting() {
    const hash = window.location.hash || "#/tickets";
    const route = hash.split("?")[0];
    const query = Object.fromEntries(new URLSearchParams(hash.split("?")[1] ||
        ""));

    switch (route) {
        case "#/tickets":
            renderTicketsList();
            break;

        case "#/ticket":
            if (!query.id) {
                $("#app").html("<p>ID do chamado não informado.</p>");
            } else {
                renderTicketDetails(Number(query.id)); // será implementado
                                                       // depois
            }
            break;

        default:
            $("#app").html("<p>Página não encontrada.</p>");
            break;
    }
}
