<script setup>
import { computed } from "vue";
import { Icon } from '@iconify/vue'
import AppLayout from "@/Layouts/AppLayout.vue";
import { Head } from "@inertiajs/vue3";
import CoverflowGallery from "@/Components/CoverflowGallery.vue";
import { Link } from "@inertiajs/vue3";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

const props = defineProps({
    resources_top: Object,
    resources_count: Number,
    topics_count: Number,
    topics_top: Object,
});

// Build an array of image URLs for the coverflow component
const galleryImages = computed(() => {
    if (!Array.isArray(props.resources_top)) return [];
    return props.resources_top.map((r) => r.image_url).filter(Boolean);
});

// Build a clean array of top topics
const topTopics = computed(() => {
    const list = Array.isArray(props.topics_top) ? props.topics_top : [];
    return list
        .map((t) => ({ id: t.tag, name: t.tag, count: t.count ?? null }))
        .filter((t) => !!t.name);
});

const fmt = (n) => new Intl.NumberFormat().format(Number(n || 0));
</script>

<template>
    <AppLayout title="Home">
        <Head>
            <title>Computer Science Resources.com</title>
            <meta
                name="description"
                content="Discover the best computer science and software engineering resources, organized for the developer community."
            />
            <meta
                property="og:title"
                content="Computer Science Resources.com"
            />
            <meta
                property="og:description"
                content="Discover the best computer science and software engineering resources, organized for the developer community."
            />
            <meta property="og:type" content="website" />
            <meta property="og:image" content="/images/og-image.png" />
            <meta
                property="og:url"
                content="https://computerscienceresources.com/"
            />
            <meta
                property="og:site_name"
                content="Computer Science Resources.com"
            />
            <link
                rel="canonical"
                href="https://computerscienceresources.com/"
            />
        </Head>
        <div
            class="bg-gradient-to-b from-primary/10 to-white dark:from-gray-900 dark:to-gray-800 min-h-screen py-12"
        >
            <div class="max-w-5xl mx-auto px-4">
                <!-- Hero Section -->
                <section class="text-center mb-12">
                    <h1
                        class="text-5xl md:text-6xl font-extrabold text-primary mb-3 tracking-tight"
                    >
                        Computer Science Resources.com
                    </h1>
                    <p
                        class="text-lg md:text-xl text-gray-700 dark:text-gray-300 mb-4 max-w-3xl mx-auto"
                    >
                        Discover curated computer science and software
                        engineering resources—organized by and for the developer
                        community.
                    </p>
                    <!-- Coverflow Gallery -->
                    <div class="mb-8">
                        <CoverflowGallery
                            v-if="galleryImages && galleryImages.length"
                            :images="galleryImages"
                            :autoplay="true"
                            :auto-speed="0.26"
                            :height="240"
                        />
                    </div>

                        <!-- Explore Resources CTA -->
                        <div class="flex justify-center mb-6">
                            <Link :href="route('resources.index')">
                                <PrimaryButton class="px-6 py-3 text-lg md:text-xl font-semibold">
                                    Explore Resources
                                </PrimaryButton>
                            </Link>
                        </div>

                    <p
                        class="mt-2 text-2xl md:text-3xl font-semibold text-gray-800 dark:text-gray-200 mb-4"
                    >
                        Over
                        <span class="text-primary">{{
                            fmt(props.resources_count)
                        }}</span>
                        resources, covering over
                        <span class="text-primary">{{
                            fmt(props.topics_count)
                        }}</span>
                        topics.
                    </p>

                    <div
                        v-if="topTopics.length"
                        class="flex flex-wrap justify-center gap-2 md:gap-3 mb-9"
                    >
                        <span
                            v-for="t in topTopics"
                            :key="t.id"
                            class="px-3 py-1.5 rounded-full bg-primary/10 text-primaryDark dark:text-primary border border-primary/20 text-sm md:text-base hover:bg-primary/15 transition-colors"
                            title="Top topic"
                        >
                            {{ t.name }}
                        </span>
                    </div>
                </section>

                <div class="mt-20">
                    <!-- Why Section -->
                    <section
                    class="bg-white dark:bg-gray-900 rounded-xl shadow-lg p-8 mb-12"
                    >
                        <h2
                        class="text-3xl font-bold text-primary mb-4 text-center"
                        >
                            Why Computer Science Resources Matter
                        </h2>
                        <p class="text-lg text-center text-gray-700 dark:text-gray-300 mb-4">
                            <b>In short:</b> Having access to high-quality resources is
                            essential for growth and innovation.
                        </p>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mt-6">
                            <!-- Hidden Gems -->
                            <div class="flex flex-col items-center text-center gap-3 p-5 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                <div class="bg-primary/10 text-primary rounded-full p-3">
                                    <Icon icon="mdi:star-outline" class="w-7 h-7" />
                                </div>
                                <h3 class="text-lg font-semibold text-primary">Hidden Gems</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Curated, lesser-known resources with big value.</p>
                            </div>

                            <!-- Keeping up with Industry Trends -->
                            <div class="flex flex-col items-center text-center gap-3 p-5 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                <div class="bg-primary/10 text-primary rounded-full p-3">
                                    <Icon icon="mdi:trending-up" class="w-7 h-7" />
                                </div>
                                <h3 class="text-lg font-semibold text-primary">Industry Trends</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Keep current with tools, frameworks, and best practices.</p>
                            </div>

                            <!-- Community Reviewed -->
                            <div class="flex flex-col items-center text-center gap-3 p-5 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                <div class="bg-primary/10 text-primary rounded-full p-3">
                                    <Icon icon="mdi:account-group" class="w-7 h-7" />
                                </div>
                                <h3 class="text-lg font-semibold text-primary">Community Reviewed</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Ratings and notes from contributors to help you choose.</p>
                            </div>

                            <!-- Discover New Concentrations -->
                            <div class="flex flex-col items-center text-center gap-3 p-5 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                <div class="bg-primary/10 text-primary rounded-full p-3">
                                    <Icon icon="mdi:compass" class="w-7 h-7" />
                                </div>
                                <h3 class="text-lg font-semibold text-primary">Discover Concentrations</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Explore niche topics and career-focused learning paths.</p>
                            </div>
                        </div>
                    </section>
                </div>

                <!-- For You Section -->
                <section class="my-12">
                    <div class="bg-white dark:bg-gray-900 rounded-xl shadow-lg p-8">
                        <h2 class="text-3xl font-bold text-primary mb-4 text-center">Computer Science Resources for You</h2>
                        <p class="text-center text-gray-700 dark:text-gray-300 mb-6 max-w-2xl mx-auto">Whether you're teaching, starting out, leading a team, or managing projects, find curated resources tailored to your role.</p>

                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-6 items-start">
                            <!-- Logo column: spans 1 on md+, full width above -->
                            <div class="flex items-center justify-center md:justify-start p-4">
                                <ApplicationLogo class="w-32 h-auto" />
                            </div>

                            <!-- Role cards: span remaining columns -->
                            <div class="md:col-span-3 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                                <!-- Tech Lead -->
                                <div class="flex flex-col items-start gap-3 p-5 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                    <h3 class="text-lg font-semibold text-primary">Tech Lead</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Find advanced guides, architecture patterns, and resources to level up your engineering leadership and spot gaps in your stack or team skills.</p>
                                </div>

                                <!-- Software Engineer -->
                                <div class="flex flex-col items-start gap-3 p-5 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                    <h3 class="text-lg font-semibold text-primary">Software Engineer</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Find deep-dive tutorials, performance tips, testing patterns, and hands-on examples to solve real-world engineering problems.</p>
                                </div>

                                <!-- Project Manager -->
                                <div class="flex flex-col items-start gap-3 p-5 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                    <h3 class="text-lg font-semibold text-primary">Project Manager</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Access practical templates, planning guides, and communication patterns to keep technical projects on track and stakeholders aligned.</p>
                                </div>

                                <!-- Teacher -->
                                <div class="flex flex-col items-start gap-3 p-5 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                    <h3 class="text-lg font-semibold text-primary">Teacher</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Discover structured curricula, lesson plans, slide decks and affordable course materials to support engaging, standards-aligned classes.</p>
                                </div>

                                <!-- University Student -->
                                <div class="flex flex-col items-start gap-3 p-5 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                    <h3 class="text-lg font-semibold text-primary">University Student</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Supplement coursework with practical labs, reference guides, and project ideas to deepen concepts and prepare for internships.</p>
                                </div>

                                <!-- Children -->
                                <div class="flex flex-col items-start gap-3 p-5 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                    <h3 class="text-lg font-semibold text-primary">Children</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Kid-friendly projects and interactive resources that introduce computing concepts through play and guided activities.</p>
                                </div>

                                <!-- Other occupation -->
                                <div class="flex flex-col items-start gap-3 p-5 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                    <h3 class="text-lg font-semibold text-primary">Other Occupational Professional</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Practical guides showing how programming and automation can help in your daily workflows. Such as reporting or data analysis.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>


            </div>
        </div>
    </AppLayout>
</template>
