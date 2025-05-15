<template>
    <Head title="Создать команду для турнира - Drop Cyber Lounge" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Создать команду для турнира
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <form @submit.prevent="submit" class="space-y-4">
                            <div>
                                <label class="text-sm text-gray-600">Название команды</label>
                                <input
                                    v-model="form.name"
                                    placeholder="Название команды"
                                    required
                                    class="w-full p-2 border rounded"
                                />
                            </div>
                            <div>
                                <label class="text-sm text-gray-600">Максимум игроков</label>
                                <select v-model="form.max_players" required class="w-full p-2 border rounded">
                                    <option v-for="n in [2, 3, 5]" :key="n" :value="n">
                                        {{ n }}
                                    </option>
                                </select>
                            </div>
                            <div>
                                <label class="text-sm text-gray-600">Участники</label>
                                <select v-model="form.user_ids" multiple required class="w-full p-2 border rounded">
                                    <option v-for="user in users" :key="user.id" :value="user.id">
                                        {{ user.name }}
                                    </option>
                                </select>
                            </div>
                            <button
                                type="submit"
                                class="rounded-md bg-green-500 px-3 py-1 text-white hover:bg-green-600"
                            >
                                Создать
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
    props: ["tournament", "users"],
    setup() {
        const form = useForm({
            name: "",
            max_players: 5,
            user_ids: [],
        });

        return { form };
    },
    methods: {
        submit() {
            this.form.post(`/tournaments/${this.tournament}/teams/create`, {
                onSuccess: () => this.form.reset(),
            });
        },
    },
};
</script>
