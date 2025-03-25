import { object, string, array } from "yup";

export const mandatoryFields = object({
    name: string().required("Name is required").max(100, "Max 100 chars"),
    page_url: string().url("Must be a valid URL").required("URL is required"),
    image_url: string().url("Must be a valid image URL"),
    platforms: array()
        .of(string())
        .min(1, "At least one platform is required"),
    description: string().required("Description is required").max(4000),
    difficulty: string().required("Difficulty level is required"),
    pricing: string().required("Pricing information is required"),
});

export const mandatoryTags = object({
    topic_tags: array()
        .of(string().trim())
        .min(3, "At least three topics are required")
        .required("Topics are required")
});

export const optionalFields = object({
    
});