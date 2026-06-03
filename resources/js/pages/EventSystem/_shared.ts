export type Paginated<T> = {
    data: T[];
    links?: { url: string | null; label: string; active: boolean }[];
};

export function money(value: number | string | null | undefined): string {
    const numericValue = Number(value ?? 0);

    return new Intl.NumberFormat('en-ZA', {
        style: 'currency',
        currency: 'USD',
    }).format(numericValue);
}

export function shortDate(value: string | null | undefined): string {
    if (!value) {
        return 'Not set';
    }

    return new Intl.DateTimeFormat('en-ZA', {
        dateStyle: 'medium',
    }).format(new Date(value));
}

export const eventStatuses = [
    'Inquiry',
    'Quoted',
    'Confirmed',
    'Completed',
    'Cancelled',
];

export const eventTypes = [
    'Wedding',
    'Birthday',
    'Corporate',
    'Funeral',
    'Graduation',
    'Other',
];
