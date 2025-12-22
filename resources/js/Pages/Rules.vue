<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Head } from "@inertiajs/vue3";
import { canonicalFor, defaultOgImage, SITE_NAME } from "@/Helpers/seo";
import { Icon } from "@iconify/vue";
</script>

<template>
    <AppLayout title="Rules">
        <!-- Page metadata -->
        <Head>
            <meta
                head-key="rules:og:title"
                property="og:title"
                :content="`Community Rules & Guidelines - ${SITE_NAME}`"
            />
            <meta
                head-key="rules:description"
                name="description"
                content="Community rules and guidelines for Computer Science Resources - learn what content is allowed, posting guidelines, and how to maintain a high-quality learning environment."
            />
            <meta
                head-key="rules:og:description"
                property="og:description"
                content="Community rules and guidelines for Computer Science Resources - learn what content is allowed, posting guidelines, and how to maintain a high-quality learning environment."
            />
            <meta
                head-key="rules:og:type"
                property="og:type"
                content="website"
            />
            <meta
                head-key="rules:og:image"
                property="og:image"
                :content="defaultOgImage()"
            />
            <link
                head-key="rules:canonical"
                rel="canonical"
                :href="canonicalFor('/rules')"
            />
            <meta
                head-key="rules:og:url"
                property="og:url"
                :content="canonicalFor('/rules')"
            />
            <meta
                head-key="rules:og:site_name"
                property="og:site_name"
                :content="SITE_NAME"
            />
        </Head>

        <main class="max-w-4xl mx-auto sm:px-6 lg:px-8 py-7">
            <div class="mb-8">
                <h1 class="text-4xl font-bold mb-3 text-balance text-primary">Community Rules & Guidelines</h1>
                <p class="text-lg text-gray-600 dark:text-gray-400 text-pretty">
                    Help us maintain a high-quality, respectful community for CS and software engineering resources.
                </p>
            </div>

            <div class="space-y-6">
                <!-- What Can Be Posted -->
                <div class="rounded-lg border bg-white dark:bg-gray-900 border-gray-200 dark:border-gray-700 text-gray-900 dark:text-gray-100 shadow-sm p-6">
                    <div class="flex items-start gap-3 mb-4">
                        <Icon icon="lucide:check-circle-2" class="h-6 w-6 text-primary mt-1 shrink-0" />
                        <div>
                            <h2 class="text-2xl font-bold mb-2">What Can Be Posted</h2>
                            <p class="text-gray-600 dark:text-gray-400 mb-4">We welcome high-quality educational resources including:</p>
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-4">
                        <div class="space-y-3">
                            <div class="flex items-start gap-2">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 bg-transparent mt-1">
                                    Allowed
                                </span>
                                <div>
                                    <p class="font-semibold">Online Courses & Tutorials</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">MOOCs, video series, interactive tutorials</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-2">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 bg-transparent mt-1">
                                    Allowed
                                </span>
                                <div>
                                    <p class="font-semibold">Books & Documentation</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Technical books, official docs, guides</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-2">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 bg-transparent mt-1">
                                    Allowed
                                </span>
                                <div>
                                    <p class="font-semibold">Research Papers</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Academic papers, whitepapers, technical reports</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-2">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 bg-transparent mt-1">
                                    Allowed
                                </span>
                                <div>
                                    <p class="font-semibold">Tools & Frameworks</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Development tools, libraries, frameworks</p>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <div class="flex items-start gap-2">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 bg-transparent mt-1">
                                    Allowed
                                </span>
                                <div>
                                    <p class="font-semibold">Practice Platforms</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Coding challenges, algorithm practice sites</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-2">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 bg-transparent mt-1">
                                    Allowed
                                </span>
                                <div>
                                    <p class="font-semibold">Open Source Projects</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Educational repos, example implementations</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-2">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 bg-transparent mt-1">
                                    Allowed
                                </span>
                                <div>
                                    <p class="font-semibold">Community Resources</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Forums, communities, study groups</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-2">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 bg-transparent mt-1">
                                    Allowed
                                </span>
                                <div>
                                    <p class="font-semibold">Cheat Sheets & References</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Quick references, syntax guides, roadmaps</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- What Cannot Be Posted -->
                <div class="rounded-lg border bg-white dark:bg-gray-900 border-red-500/50 text-gray-900 dark:text-gray-100 shadow-sm p-6">
                    <div class="flex items-start gap-3 mb-4">
                        <Icon icon="lucide:x-circle" class="h-6 w-6 text-red-500 mt-1 shrink-0" />
                        <div>
                            <h2 class="text-2xl font-bold mb-2">What Cannot Be Posted</h2>
                            <p class="text-gray-600 dark:text-gray-400 mb-4">The following types of content are not allowed:</p>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div class="flex items-start gap-2">
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-red-500 text-white mt-1">
                                Prohibited
                            </span>
                            <div>
                                <p class="font-semibold">Self-Promotion & Spam</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    Excessive self-promotion, affiliate links, or spammy content
                                </p>
                            </div>
                        </div>
                        <div class="flex items-start gap-2">
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-red-500 text-white mt-1">
                                Prohibited
                            </span>
                            <div>
                                <p class="font-semibold">Pirated Content</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    Links to pirated books, courses, or copyrighted material
                                </p>
                            </div>
                        </div>
                        <div class="flex items-start gap-2">
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-red-500 text-white mt-1">
                                Prohibited
                            </span>
                            <div>
                                <p class="font-semibold">Low-Quality or Irrelevant Content</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    Memes, off-topic content, or resources unrelated to CS/software engineering
                                </p>
                            </div>
                        </div>
                        <div class="flex items-start gap-2">
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-red-500 text-white mt-1">
                                Prohibited
                            </span>
                            <div>
                                <p class="font-semibold">Duplicate Resources</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Check if a resource already exists before posting</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-2">
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-red-500 text-white mt-1">
                                Prohibited
                            </span>
                            <div>
                                <p class="font-semibold">Misleading Information</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    False claims, clickbait titles, or deceptive descriptions
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Separator -->
                <div class="border-t border-gray-200 dark:border-gray-700"></div>

                <!-- Posting Guidelines -->
                <div class="rounded-lg border bg-white dark:bg-gray-900 border-gray-200 dark:border-gray-700 text-gray-900 dark:text-gray-100 shadow-sm p-6">
                    <div class="flex items-start gap-3 mb-4">
                        <Icon icon="lucide:alert-circle" class="h-6 w-6 text-primary mt-1 shrink-0" />
                        <div>
                            <h2 class="text-2xl font-bold mb-2">Posting Guidelines</h2>
                            <p class="text-gray-600 dark:text-gray-400">Follow these best practices when submitting resources:</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <h3 class="font-semibold mb-2">1. Use Descriptive Titles</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Include the resource name and what it covers. Avoid vague titles like "Great resource!" or "Check this out!"
                            </p>
                        </div>
                        <div>
                            <h3 class="font-semibold mb-2">2. Provide Context in Descriptions</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Explain what the resource is about, who it's for, and why it's valuable. Help others understand what they'll learn.
                            </p>
                        </div>
                        <div>
                            <h3 class="font-semibold mb-2">3. Choose the Right Category</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Select the most appropriate category to help others discover your resource.
                            </p>
                        </div>
                        <div>
                            <h3 class="font-semibold mb-2">4. Verify Links Work</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Make sure the URL is correct and accessible before submitting.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Review Guidelines -->
                <div class="rounded-lg border bg-white dark:bg-gray-900 border-gray-200 dark:border-gray-700 text-gray-900 dark:text-gray-100 shadow-sm p-6">
                    <h2 class="text-2xl font-bold mb-4">Review Guidelines</h2>
                    <div class="space-y-4">
                        <div>
                            <h3 class="font-semibold mb-2">Be Constructive</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Provide helpful feedback. Explain what you liked or didn't like and why.
                            </p>
                        </div>
                        <div>
                            <h3 class="font-semibold mb-2">One Review Per Resource</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                You can only submit one review per resource, but you can edit it anytime.
                            </p>
                        </div>
                        <div>
                            <h3 class="font-semibold mb-2">Stay Respectful</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Keep discussions civil and professional. No personal attacks or harassment.
                            </p>
                        </div>
                        <div>
                            <h3 class="font-semibold mb-2">Base Reviews on Experience</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Only review resources you've actually used or studied.</p>
                        </div>
                    </div>
                </div>

                <!-- Voting Guidelines -->
                <div class="rounded-lg border bg-white dark:bg-gray-900 border-gray-200 dark:border-gray-700 text-gray-900 dark:text-gray-100 shadow-sm p-6">
                    <h2 class="text-2xl font-bold mb-4">Voting Guidelines</h2>
                    <div class="space-y-4">
                        <div>
                            <h3 class="font-semibold mb-2">Upvote Quality Content</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Upvote resources and reviews that are helpful, well-explained, and add value to the community.
                            </p>
                        </div>
                        <div>
                            <h3 class="font-semibold mb-2">Downvote Appropriately</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Downvote content that violates rules, is misleading, or doesn't contribute meaningfully.
                            </p>
                        </div>
                        <div>
                            <h3 class="font-semibold mb-2">Don't Vote Manipulate</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Don't use multiple accounts or coordinate with others to manipulate votes.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Enforcement -->
                <div class="rounded-lg border bg-gray-50 dark:bg-gray-800/30 border-gray-200 dark:border-gray-700 text-gray-900 dark:text-gray-100 shadow-sm p-6">
                    <h2 class="text-2xl font-bold mb-4">Enforcement</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        Violations of these rules may result in content removal, warnings, or account suspension depending on the severity. Repeated violations will lead to permanent bans.
                    </p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        If you see content that violates these rules, please report it to the moderators. Thank you for helping maintain a quality community!
                    </p>
                </div>
            </div>
        </main>
    </AppLayout>
</template>
