<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import FrequentlyAskedQuestion from '@/Components/FrequentlyAskedQuestion.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
</script>

<template>
    <AppLayout title="About Us">
        <div class="py-7">
            <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6 sm:p-12">
                        <!-- Header -->
                        <div class="text-center mb-12">
                            <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
                                About ComputerScienceResources.com
                            </h1>
                        </div>

                        <!-- Main Content -->
                        <div class="prose prose-lg max-w-none text-gray-700 dark:text-gray-300 space-y-6">
                            <p>
                                Software is a unique industry — most people enter it as hobbyists, tinker with side projects, and are constantly learning.
                            </p>

                            <p>
                                Computer science is a wide field. There is a lot to learn: from data science, algorithms, databases and infrastructure.
                            </p>

                            <ApplicationLogo class="mx-auto"/>

                            <p>
                                On the plus side, there are a ton of resources that are out there. The problem? It is scattered all over the internet - buried in newsletters, YouTube playlists, GitHub repositories, and outdated blog posts.
                            </p>

                            <p>
                                It's often difficult to know what a resource actually teaches, who is the intended audience, or how well it's received by the community.
                            </p>

                            <div class="bg-secondary dark:bg-gray-700 rounded-lg p-6">
                                <p class="text-primary dark:text-accent font-semibold">
                                    Our Mission
                                </p>
                                <p class="text-gray-800 dark:text-gray-200">
                                    Our goal with ComputerScienceResources.com is to make learning easier by organizing and highlighting the best resources in computer science and software engineering. We aim to help the developer community thrive by providing a central place to discover, review, and share valuable content.
                                </p>
                            </div>

                            <p>
                                In addition, we provide the community the opportunity to give back to the website. Everyone can post resources, review resources, and suggest edits to any existing resource description.
                            </p>

                            <p>
                                On the more technical side, our github will be open to discussion and feedback soon! Don't be afraid to reach out there!
                            </p>

                            <p class="text-center text-lg font-medium text-primary dark:text-accent mt-8">
                                ComputerScienceResources.com values the community, because in the end, we all love software!
                            </p>
                        </div>

                        <!-- FAQ -->
                        <div class="mt-6 p-4">
                            <h2 class="font-bold text-center my-5 text-lg">Frequently Asked Questions</h2>
                            <FrequentlyAskedQuestion
                                question="Who is this site for?"
                                answer="Everyone!"
                            />

                            <FrequentlyAskedQuestion
                                question="Why is Computer Science Resources not open source if you love the community?"
                                answer="It will be :). We wanted to keep it closed source in the early release to prevent people from forking the codebase and stealing the project's credit. Furthermore, we need to do some refactoring to make it more appealing to those who wish to contribute to the codebase. We will be happy to open source it in the near future with ideally 200 users minimum."
                            />

                            <FrequentlyAskedQuestion
                                question="How does Computer Science Resources make money?"
                                answer="We don't plan to make anything locked behind a paywall anytime in the future. The plans for monetization will be from donations, or non-intrusive ads in the future."
                            />

                            <FrequentlyAskedQuestion
                                question="What kind of resources are accepted?"
                                answer="We focus on comprehensive, structured, and high-quality resources — things that provide long-term learning value. That includes interactive platforms, educational YouTube channels, curated book series, technical newsletters, and more. The more focused or specialized the resource is, the better."
                            />

                            <FrequentlyAskedQuestion
                                question="Can I post my own course?"
                                answer="Only if it's well-received and genuinely useful. This isn't a place to advertise low-quality content, we will take those down. We care more about substance than self-promotion. If your course is loved by the community, it's welcome."
                            />

                            <FrequentlyAskedQuestion
                                question="Is the site finished?"
                                answer="Of course not, we are still developing things. There are a lot of features that have not been implemented yet for the sake of developing a release. If I were to make the first release perfect, it wouldn't be out here. So, we will be constantly listening to community feedback and improving the app. Feel free to point out issues in the github repository tool and github discussions tab! Some things that are in the works are the ability to view someone's profile, report malicious posts and users, and favoriting a resource."
                            />

                            <FrequentlyAskedQuestion
                                question="Why is the Logo a Cat?"
                                answer="I love cats. What else can I say? I have two cats and I simply love them. I think the world is better with cats involved."
                            />

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
