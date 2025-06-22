import { object, string, array, number } from "yup";

// --------------------------
// Resource
// --------------------------
export const resourceMandatoryFields = object({
    name: string().required("Name is required").max(100, "Max 100 chars"),
    page_url: string().url("Must be a valid URL").required("URL is required"),
    image_url: string().url("Must be a valid image URL"),
    platforms: array().of(string()).min(1, "At least one platform is required"),
    description: string().required("Description is required").max(10000),
    difficulty: string().required("Difficulty level is required"),
    pricing: string().required("Pricing information is required"),
});

export const resourceMandatoryTags = object({
    topic_tags: array()
        .of(string().max(50))
        .min(3, "At least three topics are required")
        .required("Topics are required"),
});

export const optionalFields = object({
    programming_languages: array()
        .of(string().max(50)),
    general_tags: array()
            .of(string().max(50))
});

export const resourceFields = resourceMandatoryFields.concat(resourceMandatoryTags).concat(optionalFields);

// -------------------------
// Resource Reviews
// -------------------------

export const resourceReviewFields = object({
    title: string().required("Title is required."),
    description: string().required("Description is required."),
    community: number()
        .required("Community is required.")
        .min(1, "Minimum is 1.")
        .max(5, "Maximum is 5."),
    teaching_clarity: number()
        .required("Teaching clarity is required.")
        .min(1, "Minimum is 1.")
        .max(5, "Maximum is 5."),
    engagement: number()
        .required("Engagement is required.")
        .min(1, "Minimum is 1.")
        .max(5, "Maximum is 5."),
    practicality: number()
        .required("Practicality is required.")
        .min(1, "Minimum is 1.")
        .max(5, "Maximum is 5."),
    user_friendliness: number()
        .required("User friendliness is required.")
        .min(1, "Minimum is 1.")
        .max(5, "Maximum is 5."),
    updates: number()
        .required("Updates rating is required.")
        .min(1, "Minimum is 1.")
        .max(5, "Maximum is 5."),
    pros: array()
        .transform((_value, originalValue) => {
            // Handle case where PrimeVue might pass a string instead of array
            if (typeof originalValue === 'string') {
                return originalValue.trim() ? [originalValue.trim()] : [];
            }
            return Array.isArray(originalValue) ? originalValue : [];
        })
        .of(
            string()
                .max(200, "Each pro must have 200 characters or less.")
                .required("Each pro is required.")
        )
        .required("Pros are required."),
    cons: array()
        .transform((_value, originalValue) => {
            // Handle case where PrimeVue might pass a string instead of array
            if (typeof originalValue === 'string') {
                return originalValue.trim() ? [originalValue.trim()] : [];
            }
            return Array.isArray(originalValue) ? originalValue : [];
        })
        .of(
            string()
                .max(200, "Each con must have 200 characters or less.")
                .required("Each con is required.")
        )
        .required("Cons are required."),
});

// --------------------------
// Resource Edits
// --------------------------
export const resourceEditsMandatoryFields = object({
    edit_title: string().required().max(100, "Max 100 chars"),
    edit_description: string().required().max(10000),
});

export const resourceEditsFields = resourceEditsMandatoryFields.concat(resourceFields);
