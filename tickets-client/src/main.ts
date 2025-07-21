import { handleRouting } from "./router";

$(document).ready(() => {
    handleRouting();

    // Reage à mudança de hash na URL (ex: #/ticket?id=1)
    $(window).on("hashchange", () => {
        handleRouting();
    });
});

