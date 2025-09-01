// Simple SEO helpers used across pages

export const BASE_URL = "https://computerscienceresources.com";
export const SITE_NAME = "Computer Science Resources";

export function canonicalFor(path = "/") {
  const p = path.startsWith("/") ? path : `/${path}`;
  return `${BASE_URL}${p}`;
}

export function resourceCanonical(slug) {
  return canonicalFor(`/resources/${slug}`);
}

export function summarize(text, maxLen = 300) {
  return (text || "").replace(/\s+/g, " ").trim().slice(0, maxLen);
}

export function defaultOgImage() {
  return `${BASE_URL}/images/LogoTitle.svg`;
}

export function absoluteUrl(u) {
  if (!u) return null;
  if (/^https?:\/\//i.test(u)) return u;
  return `${BASE_URL}${u.startsWith("/") ? "" : "/"}${u}`;
}

export function ogImageForResource(imageUrl) {
  return absoluteUrl(imageUrl) || defaultOgImage();
}
