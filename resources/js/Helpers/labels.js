/**
 * Labels on the more visual end of things
 */
export const platformsObject = [
    { label: "Website", value: "website" },
    { label: "Book", value: "book" },
    { label: "Blog", value: "blog" },
    { label: "Course", value: "course" },
    { label: "Bootcamp", value: "bootcamp" },
    { label: "Organization", value: "organization" },
    { label: "Service", value: "service" },
    { label: "Youtube Channel", value: "youtube_channel" },
    { label: "Newsletter", value: "newsletter" },
    { label: "Podcast", value: "podcast" },
    { label: "Forum", value: "forum" },
    { label: "Workshop", value: "workshop" },
    { label: "Mobile app", value: "mobile_app" },
    { label: "Desktop app", value: "desktop_app" },
    { label: "Magazine", value: "magazine" },
];

export const platformLabels = {
    website: "Website",
    book: "Book",
    blog: "Blog",
    course: "Course",
    organization: "Organization",
    service: "Service",
    bootcamp: "Bootcamp",
    youtube_channel: "Youtube Channel",
    newsletter: "Newsletter",
    podcast: "Podcast",
    forum: "Forum",
    workshop: "Workshop",
    mobile_app: "Mobile App",
    desktop_app: "Desktop App",
    magazine: "Magazine",
};

export const pricingsObject = [
    { label: "Free", value: "free" },
    { label: "Paid", value: "paid" },
    { label: "Freemium", value: "freemium" },
    { label: "Premium", value: "premium" },
];

export const pricingLabels = {
    free: "Free",
    premium: "Premium",
    paid: "Paid",
    freemium: "Freemium",
};

export const difficultiesObject = [
    { label: "Anyone (non-technical, general audience)", value: "general" },
    { label: "Introductory (beginners, first exposure)", value: "introduction" },
    { label: "Practical (technical, industry-focused, application)", value: "practical" },
    { label: "Advanced (complex systems, deeper dives)", value: "advanced" },
    { label: "Academic (research-heavy, theoretical)", value: "academic" },
];

export const difficultyLabels = {
    general: "Anyone (non-technical, general audience)",
    introduction: "Introductory (beginners, first exposure)",
    practical: "Practical (technical, industry-focused)",
    advanced: "Advanced (complex systems, deeper dives)",
    academic: "Academic (research-heavy, theoretical)",
};

export const getPricingLabel = (pricing) => pricingLabels[pricing] || "Unknown";

export const getDifficultyLabel = (difficulty) =>
    difficultyLabels[difficulty] || "Unknown";

export const ratingLabels = {
    community: "Community",
    teaching_clarity: "Teaching Clarity",
    engagement: "Engagement",
    practicality: "Practicality",
    user_friendliness: "User Friendliness",
    updates: "Updates",
};

export const getPlatformLabel = (platform) => platformLabels[platform] || "Unknown";

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
