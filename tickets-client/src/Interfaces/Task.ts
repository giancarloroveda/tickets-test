import type Ticket from "./Ticket.ts";

export default interface Task {
    id: number;
    description: string;
    status: string;
    assignee_name: string;
    ticked_id: number;
    ticket?: Ticket;
}