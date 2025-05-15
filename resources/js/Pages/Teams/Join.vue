<template>
    <Head title="Присоединиться к команде - Drop Cyber Lounge" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Присоединиться к команде
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <form @submit.prevent="join" class="space-y-4">
                            <div>
                                <label class="text-sm text-gray-600"
                                    >Код приглашения</label
                                >
                                <input
                                    v-model="form.invite_code"
                                    placeholder="Код приглашения"
                                    required
                                    class="w-full p-2 border rounded"
                                />
                            </div>
                            <button
                                type="submit"
                                class="rounded-md bg-blue-500 px-3 py-1 text-white hover:bg-blue-600"
                            >
                                Присоединиться
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";

export default {
    components: { AuthenticatedLayout, Head },
    setup() {
        const form = useForm({
            invite_code: "",
        });

        return { form };
    },
    methods: {
        join() {
            this.form.post(route("dashboard.teams.join"));
        },
    },
};
</script>
