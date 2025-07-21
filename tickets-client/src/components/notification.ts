export function showNotification(
    message: string,
    type: "success" | "error" = "success"
) {
    const color = type === "success" ? "bg-green-500" : "bg-red-500";
    const el = $(`
    <div class="fixed top-4 right-4 z-1000 ${color} text-white px-4 py-2 rounded shadow">
      ${message}
    </div>
  `);
    $("body").append(el);
    setTimeout(() => el.fadeOut(500, () => el.remove()), 3000);
}
