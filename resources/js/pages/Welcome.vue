<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import { login, register } from '@/routes';

withDefaults(
    defineProps<{
        canRegister: boolean;
    }>(),
    {
        canRegister: true,
    },
);

const scrolled = ref(false);
const mobileMenuOpen = ref(false);

onMounted(() => {
    window.addEventListener('scroll', () => {
        scrolled.value = window.scrollY > 20;
    });
});

const stats = [
    { value: '12K+', label: 'Usuários Ativos' },
    { value: '850+', label: 'Personal Trainers' },
    { value: '50K+', label: 'Treinos Criados' },
    { value: '98%', label: 'Taxa de Satisfação' },
];

const features = [
    {
        icon: '💪',
        title: 'Montador de Treinos Inteligente',
        description: 'Crie planos de treino personalizados com nossa biblioteca inteligente de exercícios. Perfeito para trainers e atletas autodidatas.',
    },
    {
        icon: '🥗',
        title: 'Planejamento de Dieta',
        description: 'Monte planos alimentares personalizados com controle de macros. Alinhe nutrição e objetivos fitness de forma simples.',
    },
    {
        icon: '👥',
        title: 'Conexão Trainer-Aluno',
        description: 'Personal trainers podem gerenciar múltiplos alunos, atribuir treinos e acompanhar o progresso tudo em um só lugar.',
    },
    {
        icon: '📊',
        title: 'Análise de Progresso',
        description: 'Dashboards visuais com métricas de desempenho, medidas corporais e evolução de objetivos ao longo do tempo.',
    },
    {
        icon: '🏋️',
        title: 'Biblioteca de Exercícios',
        description: 'Acesse centenas de exercícios com instruções detalhadas, grupos musculares e demonstrações em vídeo.',
    },
    {
        icon: '🔄',
        title: 'Sincronização em Tempo Real',
        description: 'Alterações sincronizam instantaneamente entre trainers e alunos. Mantenha-se alinhado na sua jornada fitness.',
    },
];

const testimonials = [
    {
        name: 'Sarah Chen',
        role: 'Personal Trainer',
        avatar: 'SC',
        content: 'O FitTrack transformou a forma como gerencio meus 45+ alunos. O montador de treinos me economiza horas toda semana, e meus alunos adoram as atualizações em tempo real.',
        rating: 5,
    },
    {
        name: 'Marcus Johnson',
        role: 'Entusiasta Fitness',
        avatar: 'MJ',
        content: 'Como alguém que treina de forma independente, o FitTrack me deu a estrutura que eu precisava. O planejador de dietas me ajudou a bater meus macros consistentemente pela primeira vez.',
        rating: 5,
    },
    {
        name: 'Elena Rodriguez',
        role: 'Dono de Academia',
        avatar: 'ER',
        content: 'Migramos toda nossa academia para o FitTrack. O gerenciamento trainer-aluno é perfeito, e as taxas de retenção aumentaram 35% em apenas 3 meses.',
        rating: 5,
    },
];

const pricingPlans = [
    {
        name: 'Grátis',
        price: 'R$0',
        period: 'para sempre',
        description: 'Perfeito para começar',
        features: [
            'Até 3 planos de treino',
            'Biblioteca básica de exercícios',
            'Acompanhamento de progresso',
            '1 conexão com trainer',
        ],
        cta: 'Começar Agora',
        highlighted: false,
    },
    {
        name: 'Pro',
        price: 'R$29,90',
        period: '/mês',
        description: 'Para atletas dedicados',
        features: [
            'Planos de treino ilimitados',
            'Biblioteca completa de exercícios',
            'Análises avançadas',
            'Planejamento de dieta',
            'Suporte prioritário',
            '5 conexões com trainers',
        ],
        cta: 'Testar Grátis',
        highlighted: true,
    },
    {
        name: 'Trainer',
        price: 'R$79,90',
        period: '/mês',
        description: 'Para personal trainers profissionais',
        features: [
            'Alunos ilimitados',
            'Gerenciamento de alunos',
            'Templates de treino personalizados',
            'Atribuição em massa',
            'Relatórios de progresso',
            'Experiência com sua marca',
            'Acesso à API',
        ],
        cta: 'Testar Grátis',
        highlighted: false,
    },
];
</script>

<template>
    <Head title="FitTrack - Seu Fitness, Do Seu Jeito">
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet" />
    </Head>

    <div class="min-h-screen bg-background font-sans">
        <!-- Navigation -->
        <nav
            :class="[
                'fixed top-0 left-0 right-0 z-50 transition-all duration-300',
                scrolled
                    ? 'bg-background/80 backdrop-blur-xl shadow-sm border-b border-border'
                    : 'bg-transparent',
            ]"
        >
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-gradient-to-br from-emerald-500 to-teal-600">
                            <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <span class="text-xl font-bold text-foreground">FitTrack</span>
                    </div>

                    <div class="hidden items-center gap-8 md:flex">
                        <a href="#features" class="text-sm font-medium text-muted-foreground transition-colors hover:text-foreground">Recursos</a>
                        <a href="#testimonials" class="text-sm font-medium text-muted-foreground transition-colors hover:text-foreground">Depoimentos</a>
                        <a href="#pricing" class="text-sm font-medium text-muted-foreground transition-colors hover:text-foreground">Planos</a>
                    </div>

                    <div class="hidden items-center gap-3 md:flex">
                        <Link
                            :href="login()"
                            class="rounded-lg px-4 py-2 text-sm font-medium text-foreground transition-colors hover:bg-accent"
                        >
                            Entrar
                        </Link>
                        <Link
                            v-if="canRegister"
                            :href="register()"
                            class="rounded-lg bg-gradient-to-r from-emerald-500 to-teal-600 px-4 py-2 text-sm font-medium text-white shadow-lg shadow-emerald-500/25 transition-all hover:shadow-emerald-500/40 hover:brightness-110"
                        >
                            Começar Grátis
                        </Link>
                    </div>

                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="rounded-lg p-2 text-foreground md:hidden">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path v-if="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div v-if="mobileMenuOpen" class="border-t border-border bg-background md:hidden">
                <div class="space-y-1 px-4 pb-3 pt-2">
                    <a href="#features" class="block rounded-lg px-3 py-2 text-base font-medium text-muted-foreground hover:bg-accent hover:text-foreground">Recursos</a>
                    <a href="#testimonials" class="block rounded-lg px-3 py-2 text-base font-medium text-muted-foreground hover:bg-accent hover:text-foreground">Depoimentos</a>
                    <a href="#pricing" class="block rounded-lg px-3 py-2 text-base font-medium text-muted-foreground hover:bg-accent hover:text-foreground">Planos</a>
                    <div class="flex flex-col gap-2 border-t border-border pt-4">
                        <Link :href="login()" class="rounded-lg px-3 py-2 text-center text-base font-medium text-foreground hover:bg-accent">Entrar</Link>
                        <Link :href="register()" class="rounded-lg bg-gradient-to-r from-emerald-500 to-teal-600 px-3 py-2 text-center text-base font-medium text-white">Começar Grátis</Link>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="relative overflow-hidden pt-32 pb-20 lg:pt-40 lg:pb-32">
            <div class="absolute inset-0 -z-10">
                <div class="absolute left-1/2 top-0 h-[600px] w-[600px] -translate-x-1/2 rounded-full bg-gradient-to-br from-emerald-500/20 to-teal-500/20 blur-3xl" />
                <div class="absolute bottom-0 left-0 h-[400px] w-[400px] rounded-full bg-gradient-to-tr from-cyan-500/10 to-blue-500/10 blur-3xl" />
            </div>

            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-3xl text-center">
                    <div class="mb-6 inline-flex items-center gap-2 rounded-full border border-emerald-500/20 bg-emerald-500/10 px-4 py-1.5 text-sm font-medium text-emerald-600 dark:text-emerald-400">
                        <span class="relative flex h-2 w-2">
                            <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex h-2 w-2 rounded-full bg-emerald-500"></span>
                        </span>
                        Now com sugestões de treinos com IA
                    </div>

                    <h1 class="mb-6 text-4xl font-extrabold tracking-tight text-foreground sm:text-5xl lg:text-6xl">
                        Seu Fitness,
                        <span class="bg-gradient-to-r from-emerald-500 to-teal-600 bg-clip-text text-transparent">Do Seu Jeito</span>
                    </h1>

                    <p class="mb-8 text-lg text-muted-foreground sm:text-xl">
                        A plataforma completa para personal trainers e entusiastas fitness. Monte treinos, planeje dietas e acompanhe seu progresso — tudo em uma interface linda.
                    </p>

                    <div class="flex flex-col items-center justify-center gap-4 sm:flex-row">
                        <Link
                            :href="register()"
                            class="w-full rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 px-8 py-4 text-base font-semibold text-white shadow-xl shadow-emerald-500/25 transition-all hover:shadow-emerald-500/40 hover:brightness-110 sm:w-auto"
                        >
                            Comece Grátis Hoje
                        </Link>
                        <a
                            href="#features"
                            class="w-full rounded-xl border border-border bg-background px-8 py-4 text-base font-semibold text-foreground transition-all hover:bg-accent sm:w-auto"
                        >
                            Veja Como Funciona
                        </a>
                    </div>

                    <p class="mt-4 text-sm text-muted-foreground">
                        Sem cartão de crédito · Plano grátis disponível · Cancele quando quiser
                    </p>
                </div>

                <!-- Hero Image / Dashboard Preview -->
                <div class="mt-16">
                    <div class="relative mx-auto max-w-5xl">
                        <div class="overflow-hidden rounded-2xl border border-border bg-card shadow-2xl">
                            <div class="flex items-center gap-2 border-b border-border bg-muted/50 px-4 py-3">
                                <div class="flex gap-1.5">
                                    <div class="h-3 w-3 rounded-full bg-red-500"></div>
                                    <div class="h-3 w-3 rounded-full bg-yellow-500"></div>
                                    <div class="h-3 w-3 rounded-full bg-green-500"></div>
                                </div>
                                <div class="flex-1 text-center text-xs text-muted-foreground">app.fittrack.io/dashboard</div>
                            </div>
                            <div class="grid grid-cols-1 gap-4 bg-background p-6 md:grid-cols-3">
                                <div class="rounded-xl border border-border bg-card p-4 shadow-sm">
                                    <div class="mb-2 flex items-center justify-between">
                                        <span class="text-sm font-medium text-muted-foreground">Treino de Hoje</span>
                                        <span class="rounded-full bg-emerald-500/10 px-2 py-0.5 text-xs font-medium text-emerald-600 dark:text-emerald-400">Perna</span>
                                    </div>
                                    <div class="space-y-2">
                                        <div class="flex items-center justify-between text-sm">
                                            <span class="text-foreground">Agachamento</span>
                                            <span class="text-muted-foreground">4×12</span>
                                        </div>
                                        <div class="flex items-center justify-between text-sm">
                                            <span class="text-foreground">Afundo</span>
                                            <span class="text-muted-foreground">3×15</span>
                                        </div>
                                        <div class="flex items-center justify-between text-sm">
                                            <span class="text-foreground">Leg Press</span>
                                            <span class="text-muted-foreground">4×10</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="rounded-xl border border-border bg-card p-4 shadow-sm">
                                    <div class="mb-2 flex items-center justify-between">
                                        <span class="text-sm font-medium text-muted-foreground">Nutrição</span>
                                        <span class="rounded-full bg-blue-500/10 px-2 py-0.5 text-xs font-medium text-blue-600 dark:text-blue-400">No Caminho</span>
                                    </div>
                                    <div class="space-y-2">
                                        <div class="flex items-center justify-between text-sm">
                                            <span class="text-foreground">Calorias</span>
                                            <span class="text-muted-foreground">1.850 / 2.200</span>
                                        </div>
                                        <div class="flex items-center justify-between text-sm">
                                            <span class="text-foreground">Proteína</span>
                                            <span class="text-muted-foreground">125g / 150g</span>
                                        </div>
                                        <div class="flex items-center justify-between text-sm">
                                            <span class="text-foreground">Carboidratos</span>
                                            <span class="text-muted-foreground">180g / 250g</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="rounded-xl border border-border bg-card p-4 shadow-sm">
                                    <div class="mb-2 flex items-center justify-between">
                                        <span class="text-sm font-medium text-muted-foreground">Progresso</span>
                                        <span class="rounded-full bg-purple-500/10 px-2 py-0.5 text-xs font-medium text-purple-600 dark:text-purple-400">+8%</span>
                                    </div>
                                    <div class="space-y-2">
                                        <div class="flex items-center justify-between text-sm">
                                            <span class="text-foreground">Treinos</span>
                                            <span class="text-muted-foreground">24 este mês</span>
                                        </div>
                                        <div class="flex items-center justify-between text-sm">
                                            <span class="text-foreground">Sequência</span>
                                            <span class="text-muted-foreground">12 dias 🔥</span>
                                        </div>
                                        <div class="flex items-center justify-between text-sm">
                                            <span class="text-foreground">Meta</span>
                                            <span class="text-muted-foreground">78% completo</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-gradient-to-br from-emerald-500/30 to-teal-500/30 blur-2xl"></div>
                        <div class="absolute -bottom-4 -left-4 h-32 w-32 rounded-full bg-gradient-to-tr from-cyan-500/20 to-blue-500/20 blur-2xl"></div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <section class="border-y border-border bg-muted/30 py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-2 gap-8 md:grid-cols-4">
                    <div v-for="stat in stats" :key="stat.label" class="text-center">
                        <div class="text-3xl font-bold text-foreground sm:text-4xl">{{ stat.value }}</div>
                        <div class="mt-1 text-sm text-muted-foreground">{{ stat.label }}</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section id="features" class="py-20 lg:py-32">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-2xl text-center">
                    <h2 class="text-3xl font-bold text-foreground sm:text-4xl">Tudo Que Você Precisa para Ter Sucesso</h2>
                    <p class="mt-4 text-lg text-muted-foreground">
                        Seja você um personal trainer gerenciando alunos ou alguém construindo sua própria jornada fitness, o FitTrack tem o que você precisa.
                    </p>
                </div>

                <div class="mt-16 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <div
                        v-for="feature in features"
                        :key="feature.title"
                        class="group rounded-2xl border border-border bg-card p-6 shadow-sm transition-all hover:border-emerald-500/30 hover:shadow-lg hover:shadow-emerald-500/5"
                    >
                        <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500/10 to-teal-500/10 text-2xl">
                            {{ feature.icon }}
                        </div>
                        <h3 class="mb-2 text-lg font-semibold text-foreground">{{ feature.title }}</h3>
                        <p class="text-sm text-muted-foreground">{{ feature.description }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Testimonials Section -->
        <section id="testimonials" class="border-t border-border bg-muted/30 py-20 lg:py-32">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-2xl text-center">
                    <h2 class="text-3xl font-bold text-foreground sm:text-4xl">Amado por Milhares</h2>
                    <p class="mt-4 text-lg text-muted-foreground">
                        Veja o que nossos usuários dizem sobre a experiência com o FitTrack.
                    </p>
                </div>

                <div class="mt-16 grid gap-6 md:grid-cols-3">
                    <div
                        v-for="testimonial in testimonials"
                        :key="testimonial.name"
                        class="rounded-2xl border border-border bg-card p-6 shadow-sm"
                    >
                        <div class="mb-4 flex gap-1">
                            <svg v-for="i in testimonial.rating" :key="i" class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                        </div>
                        <p class="mb-6 text-sm text-muted-foreground">"{{ testimonial.content }}"</p>
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-emerald-500 to-teal-600 text-sm font-semibold text-white">
                                {{ testimonial.avatar }}
                            </div>
                            <div>
                                <div class="text-sm font-semibold text-foreground">{{ testimonial.name }}</div>
                                <div class="text-xs text-muted-foreground">{{ testimonial.role }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Pricing Section -->
        <section id="pricing" class="py-20 lg:py-32">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-2xl text-center">
                    <h2 class="text-3xl font-bold text-foreground sm:text-4xl">Planos Simples e Transparentes</h2>
                    <p class="mt-4 text-lg text-muted-foreground">
                        Escolha o plano ideal para você. Faça upgrade ou downgrade a qualquer momento.
                    </p>
                </div>

                <div class="mt-16 grid gap-6 md:grid-cols-3">
                    <div
                        v-for="plan in pricingPlans"
                        :key="plan.name"
                        :class="[
                            'relative rounded-2xl border p-6 shadow-sm transition-all',
                            plan.highlighted
                                ? 'border-emerald-500 bg-gradient-to-b from-emerald-500/5 to-teal-500/5 shadow-lg shadow-emerald-500/10'
                                : 'border-border bg-card',
                        ]"
                    >
                        <div v-if="plan.highlighted" class="absolute -top-3 left-1/2 -translate-x-1/2 rounded-full bg-gradient-to-r from-emerald-500 to-teal-600 px-3 py-1 text-xs font-semibold text-white">
                            Mais Popular
                        </div>

                        <div class="mb-4">
                            <h3 class="text-lg font-semibold text-foreground">{{ plan.name }}</h3>
                            <p class="text-sm text-muted-foreground">{{ plan.description }}</p>
                        </div>

                        <div class="mb-6">
                            <span class="text-4xl font-bold text-foreground">{{ plan.price }}</span>
                            <span class="text-sm text-muted-foreground">{{ plan.period }}</span>
                        </div>

                        <ul class="mb-6 space-y-3">
                            <li v-for="feature in plan.features" :key="feature" class="flex items-start gap-2 text-sm">
                                <svg class="mt-0.5 h-4 w-4 shrink-0 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="text-muted-foreground">{{ feature }}</span>
                            </li>
                        </ul>

                        <Link
                            :href="register()"
                            :class="[
                                'block w-full rounded-lg px-4 py-2.5 text-center text-sm font-semibold transition-all',
                                plan.highlighted
                                    ? 'bg-gradient-to-r from-emerald-500 to-teal-600 text-white shadow-lg shadow-emerald-500/25 hover:shadow-emerald-500/40 hover:brightness-110'
                                    : 'border border-border bg-background text-foreground hover:bg-accent',
                            ]"
                        >
                            {{ plan.cta }}
                        </Link>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="border-t border-border py-20 lg:py-32">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-500 to-teal-600 px-6 py-16 text-center shadow-2xl sm:px-12 sm:py-20">
                    <div class="absolute -right-20 -top-20 h-64 w-64 rounded-full bg-white/10 blur-3xl"></div>
                    <div class="absolute -bottom-20 -left-20 h-64 w-64 rounded-full bg-white/10 blur-3xl"></div>

                    <div class="relative">
                        <h2 class="text-3xl font-bold text-white sm:text-4xl">Pronto para Transformar seu Fitness?</h2>
                        <p class="mx-auto mt-4 max-w-2xl text-lg text-white/80">
                            Junte-se a milhares de trainers e entusiastas fitness que já estão usando o FitTrack para alcançar seus objetivos.
                        </p>
                        <div class="mt-8 flex flex-col items-center justify-center gap-4 sm:flex-row">
                            <Link
                                :href="register()"
                                class="rounded-xl bg-white px-8 py-4 text-base font-bold text-emerald-700 shadow-xl transition-all hover:bg-white/90 hover:shadow-2xl"
                            >
                                Começar Grátis
                            </Link>
                            <a
                                href="#features"
                                class="rounded-xl border border-white/30 bg-white/10 px-8 py-4 text-base font-semibold text-white backdrop-blur-sm transition-all hover:bg-white/20"
                            >
                                Saiba Mais
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="border-t border-border bg-muted/30 py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                        <div class="mb-4 flex items-center gap-2">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-emerald-500 to-teal-600">
                                <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                            <span class="text-lg font-bold text-foreground">FitTrack</span>
                        </div>
                        <p class="text-sm text-muted-foreground">
                            A plataforma completa de fitness para trainers e entusiastas.
                        </p>
                    </div>

                    <div>
                        <h4 class="mb-4 text-sm font-semibold text-foreground">Produto</h4>
                        <ul class="space-y-2 text-sm text-muted-foreground">
                            <li><a href="#features" class="transition-colors hover:text-foreground">Recursos</a></li>
                            <li><a href="#pricing" class="transition-colors hover:text-foreground">Planos</a></li>
                            <li><a href="#" class="transition-colors hover:text-foreground">Integrações</a></li>
                        </ul>
                    </div>

                    <div>
                        <h4 class="mb-4 text-sm font-semibold text-foreground">Empresa</h4>
                        <ul class="space-y-2 text-sm text-muted-foreground">
                            <li><a href="#" class="transition-colors hover:text-foreground">Sobre</a></li>
                            <li><a href="#" class="transition-colors hover:text-foreground">Blog</a></li>
                            <li><a href="#" class="transition-colors hover:text-foreground">Carreiras</a></li>
                        </ul>
                    </div>

                    <div>
                        <h4 class="mb-4 text-sm font-semibold text-foreground">Suporte</h4>
                        <ul class="space-y-2 text-sm text-muted-foreground">
                            <li><a href="#" class="transition-colors hover:text-foreground">Central de Ajuda</a></li>
                            <li><a href="#" class="transition-colors hover:text-foreground">Contato</a></li>
                            <li><a href="#" class="transition-colors hover:text-foreground">Privacidade</a></li>
                        </ul>
                    </div>
                </div>

                <div class="mt-12 border-t border-border pt-8 text-center text-sm text-muted-foreground">
                    &copy; {{ new Date().getFullYear() }} FitTrack. Todos os direitos reservados.
                </div>
            </div>
        </footer>
    </div>
</template>
