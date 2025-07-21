import type Task from "./Task.ts";

export default interface Ticket {
    id: number;
    title: string;
    description: string;
    status: string;
    tasks?: Task[];
}