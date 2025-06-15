// Simple date formatting helpers
export const formatDate = (date) => {
    if (!date) return '';
    return new Date(date).toLocaleString(navigator.language, {
        year: "numeric",
        month: "short",
        day: "numeric",
    });
};

export const formatDateTime = (date) => {
    if (!date) return '';
    return new Date(date).toLocaleString();
};
