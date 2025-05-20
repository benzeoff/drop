<script setup>
import { Head, Link } from "@inertiajs/vue3";
import { ref, onMounted } from "vue";
import gsap from "gsap"; // Импортируем GSAP

// Пропсы для управления авторизацией
defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
});

// Динамический год для футера
const currentYearRef = ref(null);
onMounted(() => {
    if (currentYearRef.value) {
        currentYearRef.value.textContent = new Date().getFullYear();
    }

    // Логика для галереи
    const stage = document.querySelector("svg");
    const imgFg = document.querySelector("#imgFg");
    const imgBg = document.querySelector("#imgBg");

    // Массив с вашими изображениями из папки public/images/gallery
    const imgs = [
        "/images/gallery/1.jpg",
        "/images/gallery/2.jpg",
        "/images/gallery/3.jpg",
    ];

    const pos = { x: window.innerWidth / 2, y: window.innerHeight / 2 };
    let isMouseMoving = false; // Флаг для отслеживания движения мыши

    // Создаём изображения для галереи
    for (let i = 0; i < imgs.length; i++) {
        const img = document.createElementNS(
            "http://www.w3.org/2000/svg",
            "image"
        );
        imgBg.appendChild(img);
        gsap.set(img, {
            attr: {
                x: "-5%",
                y: "-5%",
                width: "110%",
                height: "110%",
                href: imgs[i],
                preserveAspectRatio: "xMidYMid slice",
            },
        });
    }

    // Обработчик изменения размера окна
    window.addEventListener("resize", () => {
        pos.x = window.innerWidth / 2;
        pos.y = window.innerHeight / 2;
        if (!isMouseMoving) {
            gsap.set("circle", {
                duration: 0.3,
                attr: { cx: pos.x, cy: pos.y },
            });
        }
    });

    // Обработчики событий для SVG (добавляем поддержку touch-событий)
    stage.addEventListener("mouseenter", () => {
        loop.pause();
        stage.addEventListener("mousemove", mouseFollow);
        stage.addEventListener("touchmove", touchFollow); // Добавляем поддержку touch
        mouseClickOn();
    });

    stage.addEventListener("mouseleave", () => {
        mouseClickOff();
        stage.removeEventListener("mousemove", mouseFollow);
        stage.removeEventListener("touchmove", touchFollow);
        isMouseMoving = false; // Сбрасываем флаг при уходе мыши
        pos.x = window.innerWidth / 2;
        pos.y = window.innerHeight / 2;
        gsap.to("circle", {
            attr: { cx: pos.x, cy: pos.y },
            ease: "power2.inOut",
        });
        gsap.to(imgFg.children[0], { attr: { x: "-5%", y: "-5%" } });
        loop.play(0);
    });

    function mouseClickOn() {
        stage.addEventListener("mousedown", maskConstrict);
        stage.addEventListener("mouseup", nextImg);
        stage.addEventListener("touchstart", maskConstrict); // Добавляем поддержку touch
        stage.addEventListener("touchend", nextImg);
    }

    function mouseClickOff() {
        stage.removeEventListener("mousedown", maskConstrict);
        stage.removeEventListener("mouseup", nextImg);
        stage.removeEventListener("touchstart", maskConstrict);
        stage.removeEventListener("touchend", nextImg);
    }

    function mouseFollow(e) {
        isMouseMoving = true;
        pos.x = e.pageX;
        pos.y = e.pageY;
        gsap.to("circle", { duration: 0.3, attr: { cx: pos.x, cy: pos.y } });
        gsap.to(imgFg.children[0], {
            attr: {
                x: gsap.utils.interpolate(
                    "0%",
                    "-10%",
                    pos.x / window.innerWidth
                ),
                y: gsap.utils.interpolate(
                    "0%",
                    "-10%",
                    pos.y / window.innerHeight
                ),
            },
        });

        clearTimeout(window.mouseMoveTimeout);
        window.mouseMoveTimeout = setTimeout(() => {
            isMouseMoving = false;
            if (!stage.matches(":hover")) return;
            pos.x = window.innerWidth / 2;
            pos.y = window.innerHeight / 2;
            gsap.to("circle", {
                attr: { cx: pos.x, cy: pos.y },
                ease: "power2.inOut",
            });
            gsap.to(imgFg.children[0], { attr: { x: "-5%", y: "-5%" } });
        }, 100);
    }

    function touchFollow(e) {
        e.preventDefault(); // Предотвращаем скролл на мобильных
        const touch = e.touches[0];
        isMouseMoving = true;
        pos.x = touch.pageX;
        pos.y = touch.pageY;
        gsap.to("circle", { duration: 0.3, attr: { cx: pos.x, cy: pos.y } });
        gsap.to(imgFg.children[0], {
            attr: {
                x: gsap.utils.interpolate(
                    "0%",
                    "-10%",
                    pos.x / window.innerWidth
                ),
                y: gsap.utils.interpolate(
                    "0%",
                    "-10%",
                    pos.y / window.innerHeight
                ),
            },
        });

        clearTimeout(window.mouseMoveTimeout);
        window.mouseMoveTimeout = setTimeout(() => {
            isMouseMoving = false;
            pos.x = window.innerWidth / 2;
            pos.y = window.innerHeight / 2;
            gsap.to("circle", {
                attr: { cx: pos.x, cy: pos.y },
                ease: "power2.inOut",
            });
            gsap.to(imgFg.children[0], { attr: { x: "-5%", y: "-5%" } });
        }, 100);
    }

    function maskConstrict() {
        gsap.to("circle", { duration: 0.3, attr: { r: (i) => [30, 50][i] } });
    }

    function nextImg() {
        mouseClickOff();
        gsap.timeline()
            .to("circle", {
                duration: 0.4,
                attr: { r: window.innerWidth },
                ease: "power3.in",
                stagger: -0.1,
            })
            .add(() => {
                imgFg.append(imgBg.children[imgBg.children.length - 1]);
                imgBg.prepend(imgFg.children[0]);
                gsap.set("circle", { attr: { r: 0 } });
            })
            .fromTo(
                "circle",
                { attr: { r: 0, cx: pos.x, cy: pos.y } },
                {
                    attr: { r: (i) => [35, 45][i] },
                    ease: "power2.inOut",
                    stagger: -0.1,
                },
                0.5
            )
            .add(mouseClickOn);
    }

    // Инициализация галереи
    imgFg.append(imgBg.children[imgBg.children.length - 1]);
    gsap.fromTo(
        "circle",
        { attr: { cx: pos.x, cy: pos.y } },
        { attr: { r: (i) => [35, 45][i] }, ease: "power2.inOut" }
    );

    const loop = gsap
        .timeline({ repeatRefresh: true, repeat: -1 })
        .add(maskConstrict, 3)
        .add(nextImg, 3.15);
});
</script>

<template>
    <Head title="О клубе - Drop Cyber Lounge" />
    <div
        class="bg-gray-50 text-black/50 dark:bg-black dark:text-white/50 min-h-screen flex flex-col"
    >
        <!-- Галерея в качестве фона -->
        <div id="container" class="w-full">
            <svg width="100%" height="100%">
                <mask id="m">
                    <circle id="c1" cx="50%" cy="50%" r="0" fill="#fff" />
                    <circle
                        id="c0"
                        cx="50%"
                        cy="50%"
                        r="0"
                        fill="#fff"
                        opacity="0.5"
                    />
                </mask>
                <g id="imgFg"></g>
                <g id="imgBg" mask="url(#m)"></g>
            </svg>
        </div>

        <div class="relative w-full max-w-7xl mx-auto flex-1 px-6">
            <!-- Шапка -->
            <header
                class="grid grid-cols-2 items-center gap-2 py-6 lg:grid-cols-3"
            >
                <div class="flex lg:col-start-2 lg:justify-center">
                    <img
                        src="/images/drop-logo3.png"
                        alt="Drop Club Logo"
                        class="h-24 w-auto transition-all hover:scale-105 lg:h-36"
                    />
                </div>
                <nav
                    v-if="canLogin"
                    class="-mx-2 flex flex-1 justify-end space-x-1 lg:space-x-2"
                >
                    <Link
                        :href="route('about')"
                        class="rounded-md px-2 py-1 text-sm lg:px-3 lg:py-2 lg:text-base text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                    >
                        О клубе
                    </Link>
                    <Link
                        :href="route('booking')"
                        class="rounded-md px-2 py-1 text-sm lg:px-3 lg:py-2 lg:text-base text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                    >
                        Бронирования
                    </Link>
                    <Link
                        :href="route('tournaments')"
                        class="rounded-md px-2 py-1 text-sm lg:px-3 lg:py-2 lg:text-base text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                    >
                        Турниры
                    </Link>
                    <Link
                        v-if="$page.props.auth.user"
                        :href="route('dashboard')"
                        class="rounded-md px-2 py-1 text-sm lg:px-3 lg:py-2 lg:text-base text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                    >
                        Профиль
                    </Link>
                    <template v-else>
                        <Link
                            :href="route('login')"
                            class="rounded-md px-2 py-1 text-sm lg:px-3 lg:py-2 lg:text-base text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                        >
                            Войти
                        </Link>
                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="rounded-md px-2 py-1 text-sm lg:px-3 lg:py-2 lg:text-base text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                        >
                            Регистрация
                        </Link>
                    </template>
                </nav>
            </header>

            <!-- Основной контент -->
            <main class="mt-4 lg:mt-6">
                <h1
                    class="text-center text-2xl font-semibold text-black dark:text-white mb-6 lg:text-3xl lg:mb-8"
                >
                    О клубе Drop Cyber Lounge
                </h1>
                <div
                    class="bg-white dark:bg-zinc-900 rounded-lg shadow-lg p-4 lg:p-6"
                >
                    <p class="text-sm/relaxed text-black dark:text-white mb-4">
                        Кибер-лаундж Drop — это современное пространство для
                        геймеров в самом сердце Сыктывкара. Мы предлагаем 20
                        мощных компьютеров с видеокартами RTX 3070, мониторами
                        144 Гц и удобными креслами, чтобы вы могли полностью
                        погрузиться в игровой процесс.
                    </p>
                    <p class="text-sm/relaxed text-black dark:text-white mb-4">
                        Наш клуб работает круглосуточно, чтобы вы могли играть в
                        любое время дня и ночи. Мы регулярно проводим турниры по
                        популярным играм, таким как CS:GO, Dota 2 и Valorant, с
                        ценными призами для победителей.
                    </p>
                    <p class="text-sm/relaxed text-black dark:text-white">
                        Присоединяйтесь к нам по адресу: Интернациональная ул.,
                        32, Сыктывкар. Забронируйте место прямо сейчас и станьте
                        частью нашего сообщества!
                    </p>
                </div>
            </main>
        </div>

        <!-- Футер -->
        <footer
            class="text-sm text-black dark:text-white/70 w-full"
            style="position: fixed; bottom: 0; left: 0; right: 0; z-index: 2"
        >
            <div
                class="flex flex-col lg:flex-row justify-between items-start gap-4 max-w-7xl mx-auto px-4 lg:px-6"
            >
                <!-- Мини-карта (слева) -->
                <div class="flex-shrink-0">
                    <iframe
                        src="https://yandex.com/map-widget/v1/?um=constructor%3Ayour_map_id&z=15&ll=50.836496%2C61.667614&mode=placemark"
                        width="200"
                        height="150"
                        frameborder="0"
                        class="rounded-lg lg:w-[300px] lg:h-[200px]"
                    ></iframe>
                </div>

                <!-- Контакты (справа) -->
                <div class="flex flex-col items-start gap-2 lg:gap-4">
                    <div class="flex items-center gap-2">
                        <img
                            src="/images/social/tel.png"
                            alt="Phone Icon"
                            class="w-5 h-5 lg:w-6 lg:h-6"
                        />
                        <a
                            href="tel:+79999999999"
                            class="underline hover:text-[#FF2D20] text-xs lg:text-sm"
                        >
                            +7 (999) 999-99-99
                        </a>
                    </div>
                    <a
                        href="https://vk.com/dropgames11?from=groups"
                        target="_blank"
                        class="flex items-center gap-2"
                    >
                        <img
                            src="/images/social/vk.png"
                            alt="VK Icon"
                            class="w-5 h-5 lg:w-6 lg:h-6"
                        />
                    </a>
                </div>
            </div>

            <!-- Компьютерный клуб © (по центру снизу) -->
            <div class="text-center text-xs lg:text-sm">
                Компьютерный клуб © <span ref="currentYearRef"></span>
            </div>
        </footer>
    </div>
</template>

<style scoped>
/* Стили для галереи */
#container {
    height: 100vh;
    overflow-y: auto;
    -webkit-overflow-scrolling: touch;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    z-index: 0; /* Галерея на заднем плане */
    touch-action: pan-y; /* Улучшаем скролл на мобильных */
}

svg {
    position: fixed;
}

/* Убедимся, что контент поверх галереи читаем */
header,
main {
    position: relative;
    z-index: 1;
}

.bg-white,
.dark\:bg-zinc-900 {
    background: rgba(
        255,
        255,
        255,
        0.9
    ); /* Полупрозрачный фон для читаемости текста */
}

.dark .bg-white,
.dark .dark\:bg-zinc-900 {
    background: rgba(39, 39, 42, 0.9); /* Полупрозрачный тёмный фон */
}

/* Стили для прижатия футера */
.min-h-screen {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
}

.w-full {
    flex: 1 0 auto;
    max-width: 7xl;
    margin: 0 auto;
}

/* Центрирование содержимого */
.mx-auto {
    margin-left: auto;
    margin-right: auto;
}

/* Адаптивные стили для мобильных устройств */
@media (max-width: 1024px) {
    /* Шапка */
    header {
        padding-top: 1rem;
        padding-bottom: 1rem;
    }

    nav {
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    /* Основной контент */
    main {
        margin-top: 1rem;
    }

    h1 {
        font-size: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .bg-white,
    .dark\:bg-zinc-900 {
        padding: 1rem;
    }

    /* Футер */
    footer {
        padding: 0.5rem 0; /* Минимальные отступы */
    }

    footer .flex-col {
        gap: 1rem;
    }

    footer iframe {
        width: 100%;
        height: 120px;
    }

    footer .text-center {
        font-size: 0.75rem;
    }
}

@media (max-width: 640px) {
    /* Шапка */
    header {
        grid-template-columns: 1fr;
        text-align: center;
    }

    header img {
        margin: 0 auto;
        height: 20vw; /* Уменьшаем логотип пропорционально */
        max-height: 100px;
    }

    nav {
        justify-content: center;
        margin-top: 1rem;
    }

    /* Основной контент */
    h1 {
        font-size: 1.25rem;
    }

    /* Футер */
    footer .flex-col {
        flex-direction: column;
        align-items: center;
        text-align: center;
    }

    footer .flex-col > div {
        margin: 0 auto;
    }
}
</style>
