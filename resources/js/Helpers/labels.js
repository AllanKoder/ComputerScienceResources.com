/**
 * Labels on the more visual end of things
 *
 */
export const platforms = [
    { label: "Website", value: "website" },
    { label: "Book", value: "book" },
    { label: "Blog", value: "blog" },
    { label: "Course", value: "course" },
    { label: "Bootcamp", value: "bootcamp" },
    { label: "Youtube Channel", value: "youtube_channel" },
    { label: "Newsletter", value: "newsletter" },
    { label: "Podcast", value: "podcast" },
    { label: "Forum", value: "forum" },
    { label: "Workshop", value: "workshop" },
    { label: "Mobile app", value: "mobile_app" },
    { label: "Desktop app", value: "desktop_app" },
    { label: "Magazine", value: "magazine" },
];

export const pricings = [
    { label: "Free", value: "free" },
    { label: "Paid", value: "paid" },
    { label: "Freemium", value: "freemium" },
];

export const difficulties = [
    { label: "Beginners - Non-technical", value: "beginner" },
    { label: "Industry Simple - Jr Engineer", value: "industry_simple" },
    { label: "Industry Standard - Engineer", value: "industry_standard" },
    {
        label: "Industry Professional - Senior Engineer",
        value: "industry_professional",
    },
    { label: "Academic - Research", value: "academic" },
];

export const difficultyLabels = {
    beginner: "Beginner",
    industry_simple: "Industry Simple",
    industry_standard: "Industry Standard",
    industry_professional: "Industry Professional",
    academic: "Academic",
};

export const pricingLabels = {
    free: "Free",
    premium: "Premium",
    paid: "Paid",
    freemium: "Freemium",
};

export const ratingLabels = {
    community: "Community",
    teaching_clarity: "Teaching Clarity",
    engagement: "Engagement",
    practicality: "Practicality",
    user_friendliness: "User Friendliness",
    updates: "Updates",
};

/// Sorting
export const resourceSortingLabels = [
    { value: "top", label: "Top Votes (Best Score)" },
    { value: "controversial", label: "Controversial (Mixed Votes)" },
    { value: "total_votes", label: "Most Voted (Negative + Postive)" },
    { value: "hot", label: "Hot (Trending Now)" },
    { value: "latest", label: "Newest (Date Created)" },
    { value: "recently_updated", label: "Recently Updated" },
    { value: "community", label: "Best Community" },
    { value: "teaching_clarity", label: "Teaching Clarity" },
    { value: "engagement", label: "Engagement" },
    { value: "practicality", label: "Practicality" },
    { value: "user_friendliness", label: "User Friendliness" },
    { value: "updates", label: "Most Updates" },
    { value: "overall", label: "Overall Rating" },
];

/// Aesthetics
export const platformColors = {
    book: "blue",
    podcast: "green",
    youtube_channel: "red",
    blog: "orange",
    website: "purple",
    organization: "cyan",
    bootcamp: "pink",
    newsletter: "indigo",
    workshop: "teal",
    course: "yellow",
    forum: "gray",
    mobile_app: "lime",
    desktop_app: "amber",
    magazine: "rose",
};
