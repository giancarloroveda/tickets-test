const BASE_URL = "http://localhost:8080";

export const api = {
    get: (url: string) =>
        fetch(`${BASE_URL}${url}`, {
            method: "GET",
            credentials: "include"
        }).then(res => res.json()),
    post: (url: string, body: any) =>
        fetch(`${BASE_URL}${url}`, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            credentials: "include",
            body: JSON.stringify(body)
        }).then(res => res.json()),
    put: (url: string, body: any) =>
        fetch(`${BASE_URL}${url}`, {
            method: "PUT",
            headers: { "Content-Type": "application/json" },
            credentials: "include",
            body: JSON.stringify(body)
        }).then(res => res.json()),
    delete: (url: string) =>
        fetch(`${BASE_URL}${url}`, {
            method: "DELETE",
            credentials: "include"
        }).then(res => res.json())
};
