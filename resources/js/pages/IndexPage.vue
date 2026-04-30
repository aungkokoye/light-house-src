<template>
    <!-- Nav -->
    <header class="fixed top-0 inset-x-0 z-50 bg-white/90 backdrop-blur border-b border-gray-100">
        <div class="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <img :src="'/images/logo.png'" alt="Lighthouse Printing Solutions" class="h-12 w-auto" />
                <div class="flex flex-col leading-tight">
                    <span class="text-sm font-bold text-red-600 tracking-tight">LightHouse</span>
                    <span class="text-xs font-medium text-gray-400 tracking-wide">Printing Solutions</span>
                </div>
            </div>

            <nav class="hidden md:flex items-center gap-8 text-sm text-gray-500">
                <a href="#services" class="hover:text-gray-900 transition-colors">Services</a>
                <a href="#portfolio" class="hover:text-gray-900 transition-colors">Portfolio</a>
                <a href="#why-us" class="hover:text-gray-900 transition-colors">Why Us</a>
                <a href="#contact" class="hover:text-gray-900 transition-colors">Contact</a>
            </nav>

            <div class="flex items-center gap-3">
                <template v-if="isLoggedIn">
                    <RouterLink to="/dashboard" class="text-sm font-medium text-blue-700 hover:text-blue-800 transition-colors">Dashboard</RouterLink>
                    <RouterLink to="/profile" class="flex items-center gap-2 text-sm text-gray-600 hover:text-gray-900 transition-colors">
                        <div class="w-7 h-7 rounded-lg bg-blue-700 flex items-center justify-center text-white text-xs font-bold">
                            {{ userInitials }}
                        </div>
                        <span v-if="userName" class="hidden sm:block font-medium">{{ userName }}</span>
                    </RouterLink>
                    <button @click="logout" class="text-sm text-gray-500 hover:text-gray-900 transition-colors">Log out</button>
                </template>
                <template v-else>
                    <RouterLink to="/login" class="text-sm text-gray-500 hover:text-gray-900 transition-colors">Log in</RouterLink>
                    <a href="#contact" class="text-sm font-medium bg-blue-700 text-white px-4 py-2 rounded-lg hover:bg-blue-800 transition-colors">
                        Get a Quote
                    </a>
                </template>
            </div>
        </div>
    </header>

    <!-- Hero -->
    <!-- Save the Facebook cover photo to public/images/hero.jpg to use as background -->
    <section class="min-h-screen flex items-center relative overflow-hidden pt-16">
        <!-- Background image (Bagan cover photo) — enable by saving to public/images/hero.jpg -->
        <img :src="'/images/hero.png'" alt="" class="absolute inset-0 w-full h-full object-cover"
            @error="(e) => e.target.style.display='none'" />
        <!-- Dark overlay -->
        <div class="absolute inset-0 bg-gradient-to-r from-slate-900/92 via-blue-900/80 to-slate-900/60"></div>

        <div class="relative max-w-6xl mx-auto px-6 py-28 grid md:grid-cols-2 gap-12 items-center">
            <div class="text-left">
                <div class="inline-flex items-center gap-2 bg-white/10 border border-white/20 rounded-full px-4 py-1.5 mb-6">
                    <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>
                    <span class="text-xs font-semibold text-white tracking-widest uppercase">Yangon, Myanmar</span>
                </div>
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white leading-tight mb-4">
                    <span class="text-red-400">LIGHTHOUSE</span><br />
                    Printing Solutions
                </h1>
                <p class="text-lg text-blue-100 mb-3 font-medium">
                    Offset · Branding · Color Print<br />Large Format · Promotional Products · Silk Screen
                </p>
                <p class="text-gray-300 mb-8 leading-relaxed">
                    Offering the widest range of quality printing services with budget effective pricing — all under one roof in Yangon.
                </p>
                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="#contact"
                        class="inline-flex items-center justify-center gap-2 bg-red-600 text-white font-semibold px-7 py-3.5 rounded-xl hover:bg-red-700 transition-colors shadow-lg">
                        Get a Free Quote
                    </a>
                    <a href="#portfolio"
                        class="inline-flex items-center justify-center gap-2 text-white font-medium px-7 py-3.5 rounded-xl border border-white/30 hover:bg-white/10 transition-colors">
                        View Portfolio
                    </a>
                </div>

                <!-- Contact bar -->
                <div class="mt-10 flex flex-col sm:flex-row gap-4 text-sm text-blue-200">
                    <a href="tel:+959782275275" class="flex items-center gap-2 hover:text-white transition-colors">
                        <svg class="w-4 h-4 text-red-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                        </svg>
                        (+95) 9 782 275 275
                    </a>
                    <a href="mailto:info@lighthouse-print.com" class="flex items-center gap-2 hover:text-white transition-colors">
                        <svg class="w-4 h-4 text-red-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                        </svg>
                        info@lighthouse-print.com
                    </a>
                </div>
            </div>

            <!-- Stats -->
            <div class="hidden md:grid grid-cols-2 gap-4">
                <div v-for="stat in stats" :key="stat.label"
                    class="bg-white/10 backdrop-blur border border-white/20 rounded-2xl p-5 text-white">
                    <p class="text-3xl font-bold text-red-400">{{ stat.value }}</p>
                    <p class="text-sm text-blue-100 mt-1 font-medium">{{ stat.label }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">{{ stat.sub }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Services -->
    <section id="services" class="py-24 bg-white overflow-hidden">
        <div class="max-w-6xl mx-auto px-6">

            <div class="text-center mb-16 bento-header">
                <span class="text-xs font-semibold tracking-widest text-red-600 uppercase">What We Offer</span>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mt-2 mb-4">Our Printing Services</h2>
                <p class="text-gray-500 max-w-xl mx-auto">From business cards to large-format signage and silk screen printing — everything under one roof.</p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4 md:[grid-auto-rows:200px]">

                <!-- Large Format — hero 2×2 -->
                <div data-bento="0" class="bento-item col-span-2 md:row-span-2 relative overflow-hidden rounded-2xl md:rounded-3xl bg-gradient-to-br from-blue-600 to-indigo-800 p-6 md:p-8 flex flex-col justify-between group cursor-default min-h-[220px] md:min-h-0">
                    <div class="bento-shimmer"></div>
                    <div class="absolute -right-8 -top-8 w-44 h-44 rounded-full bg-white/5 pointer-events-none"></div>
                    <div class="absolute right-4 -bottom-10 w-28 h-28 rounded-full bg-white/5 pointer-events-none"></div>
                    <div class="relative">
                        <div class="w-12 h-12 md:w-14 md:h-14 rounded-xl md:rounded-2xl bg-white/15 flex items-center justify-center mb-3 md:mb-4 group-hover:scale-110 transition-transform duration-300">
                            <component :is="IconLarge" class="w-6 h-6 md:w-7 md:h-7 text-white" />
                        </div>
                        <h3 class="text-lg md:text-xl font-bold text-white mb-1 md:mb-2">Large Format Printing</h3>
                        <p class="text-blue-100 text-xs md:text-sm leading-relaxed max-w-xs">Banners, light boxes, backlit prints, canvas, PP board, and large display materials.</p>
                    </div>
                    <div class="relative flex items-end justify-between mt-4 md:mt-0">
                        <div>
                            <div class="text-3xl md:text-4xl font-black text-white tabular-nums">{{ heroCount }}<span class="text-xl md:text-2xl">+</span></div>
                            <div class="text-blue-200 text-xs mt-0.5">Large format jobs delivered</div>
                        </div>
                        <div class="w-9 h-9 rounded-full bg-white/10 flex items-center justify-center group-hover:bg-white/20 transition-colors">
                            <svg class="w-4 h-4 text-white group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5l15-15m0 0H8.25m11.25 0v11.25" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Color Print — 1×1 -->
                <div data-bento="1" class="bento-item relative overflow-hidden rounded-2xl bg-purple-50 border border-purple-100 p-5 flex flex-col group cursor-default hover:shadow-lg hover:shadow-purple-100 transition-all duration-300 min-h-[160px] md:min-h-0">
                    <div class="w-10 h-10 rounded-xl bg-purple-600 flex items-center justify-center mb-3 group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300 shrink-0">
                        <component :is="IconColor" class="w-5 h-5 text-white" />
                    </div>
                    <h3 class="font-semibold text-gray-900 text-sm mb-1">Color Print</h3>
                    <p class="text-xs text-gray-500 leading-relaxed">Vivid full-colour digital prints for brochures, flyers, and posters.</p>
                </div>

                <!-- Branding — 1×2 tall -->
                <div data-bento="2" class="bento-item md:row-span-2 relative overflow-hidden rounded-2xl bg-gradient-to-br from-red-500 to-rose-700 p-5 md:p-6 flex flex-col justify-between group cursor-default min-h-[160px] md:min-h-0">
                    <div class="absolute -left-4 -bottom-6 w-28 h-28 rounded-full bg-white/5 pointer-events-none"></div>
                    <div>
                        <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform duration-300">
                            <component :is="IconBrand" class="w-5 h-5 text-white" />
                        </div>
                        <h3 class="font-bold text-white mb-2">Branding</h3>
                        <p class="text-red-100 text-xs leading-relaxed">Full branding packages — logos, stationery, and branded print collateral for your business.</p>
                    </div>
                    <div class="hidden md:block">
                        <div class="flex gap-1.5 mt-4">
                            <div class="w-3 h-3 rounded-full bg-white/50"></div>
                            <div class="w-3 h-3 rounded-full bg-white/25"></div>
                            <div class="w-3 h-3 rounded-full bg-white/10"></div>
                        </div>
                        <p class="text-red-200 text-xs mt-2">Identity that stands out</p>
                    </div>
                </div>

                <!-- Silk Screen — 1×1 -->
                <div data-bento="3" class="bento-item relative overflow-hidden rounded-2xl bg-indigo-50 border border-indigo-100 p-5 flex flex-col group cursor-default hover:shadow-lg hover:shadow-indigo-100 transition-all duration-300 min-h-[160px] md:min-h-0">
                    <div class="w-10 h-10 rounded-xl bg-indigo-600 flex items-center justify-center mb-3 group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300 shrink-0">
                        <component :is="IconScreen" class="w-5 h-5 text-white" />
                    </div>
                    <h3 class="font-semibold text-gray-900 text-sm mb-1">Silk Screen</h3>
                    <p class="text-xs text-gray-500 leading-relaxed">Durable screen printing on fabric, apparel, and promotional items.</p>
                </div>

                <!-- Offset Printing — 1×1 -->
                <div data-bento="4" class="bento-item relative overflow-hidden rounded-2xl bg-slate-800 p-5 flex flex-col group cursor-default hover:shadow-lg hover:shadow-slate-300 transition-all duration-300 min-h-[160px] md:min-h-0">
                    <div class="absolute -right-3 -bottom-3 w-16 h-16 rounded-full bg-white/5 pointer-events-none"></div>
                    <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center mb-3 group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300 shrink-0">
                        <component :is="IconOffset" class="w-5 h-5 text-white" />
                    </div>
                    <h3 class="font-semibold text-white text-sm mb-1">Offset Printing</h3>
                    <p class="text-xs text-slate-400 leading-relaxed">High-volume, consistent colour accuracy for books and catalogues.</p>
                </div>

                <!-- Business Cards — 2×1 wide -->
                <div data-bento="5" class="bento-item col-span-2 relative overflow-hidden rounded-2xl bg-gradient-to-r from-slate-800 to-slate-900 p-5 md:p-6 flex items-center gap-4 md:gap-5 group cursor-default hover:shadow-xl hover:shadow-slate-300 transition-all duration-300 min-h-[160px] md:min-h-0">
                    <div class="absolute right-0 top-0 bottom-0 w-24 bg-gradient-to-l from-white/5 to-transparent pointer-events-none"></div>
                    <div class="w-11 h-11 md:w-12 md:h-12 rounded-2xl bg-white/10 flex items-center justify-center shrink-0 group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300">
                        <component :is="IconCard" class="w-5 h-5 md:w-6 md:h-6 text-white" />
                    </div>
                    <div class="relative min-w-0">
                        <h3 class="font-bold text-white mb-1 text-sm md:text-base">Business Cards</h3>
                        <p class="text-xs text-slate-300 leading-relaxed">Matte, gloss, spot UV — professional cards that make the right first impression.</p>
                        <div class="flex gap-1.5 mt-2">
                            <span class="text-xs bg-white/10 text-white/70 rounded-full px-2 py-0.5">Matte</span>
                            <span class="text-xs bg-white/10 text-white/70 rounded-full px-2 py-0.5">Gloss</span>
                            <span class="text-xs bg-white/10 text-white/70 rounded-full px-2 py-0.5">Spot UV</span>
                        </div>
                    </div>
                </div>

                <!-- Stickers & Labels — 1×1 -->
                <div data-bento="6" class="bento-item relative overflow-hidden rounded-2xl bg-green-50 border border-green-100 p-5 flex flex-col group cursor-default hover:shadow-lg hover:shadow-green-100 transition-all duration-300 min-h-[160px] md:min-h-0">
                    <div class="w-10 h-10 rounded-xl bg-green-600 flex items-center justify-center mb-3 group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300 shrink-0">
                        <component :is="IconSticker" class="w-5 h-5 text-white" />
                    </div>
                    <h3 class="font-semibold text-gray-900 text-sm mb-1">Stickers & Labels</h3>
                    <p class="text-xs text-gray-500 leading-relaxed">Floor stickers, frosted stickers, product labels in any shape.</p>
                </div>

                <!-- Promotional Products — 2×1 wide -->
                <div data-bento="7" class="bento-item col-span-2 relative overflow-hidden rounded-2xl bg-gradient-to-br from-orange-500 to-amber-600 p-5 md:p-6 flex items-center gap-4 md:gap-5 group cursor-default hover:shadow-lg hover:shadow-orange-200 transition-all duration-300 min-h-[160px] md:min-h-0">
                    <div class="absolute -right-4 -bottom-4 w-24 h-24 rounded-full bg-white/10 pointer-events-none"></div>
                    <div class="w-11 h-11 md:w-12 md:h-12 rounded-2xl bg-white/20 flex items-center justify-center shrink-0 group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300">
                        <component :is="IconPromo" class="w-5 h-5 md:w-6 md:h-6 text-white" />
                    </div>
                    <div class="relative">
                        <h3 class="font-bold text-white mb-1 text-sm md:text-base">Promotional Products</h3>
                        <p class="text-xs text-orange-100 leading-relaxed">Counter stands, display materials, and branded items for events and marketing.</p>
                    </div>
                </div>

                <!-- Packaging — 2×1 wide -->
                <div data-bento="8" class="bento-item col-span-2 relative overflow-hidden rounded-2xl bg-amber-50 border border-amber-200 p-5 md:p-6 flex items-center gap-4 md:gap-5 group cursor-default hover:shadow-lg hover:shadow-amber-100 transition-all duration-300 min-h-[160px] md:min-h-0">
                    <div class="w-11 h-11 md:w-12 md:h-12 rounded-2xl bg-amber-600 flex items-center justify-center shrink-0 group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300">
                        <component :is="IconPackage" class="w-5 h-5 md:w-6 md:h-6 text-white" />
                    </div>
                    <div class="relative min-w-0">
                        <h3 class="font-bold text-gray-900 mb-1 text-sm md:text-base">Packaging</h3>
                        <p class="text-xs text-gray-500 leading-relaxed">Branded boxes, bags, and custom packaging for retail and gifting purposes.</p>
                    </div>
                    <div class="ml-auto hidden md:flex gap-2 shrink-0">
                        <div class="w-8 h-8 rounded-lg bg-amber-200 flex items-center justify-center"><div class="w-3 h-3 rounded-sm bg-amber-600"></div></div>
                        <div class="w-8 h-8 rounded-lg bg-amber-200 flex items-center justify-center"><div class="w-4 h-2 rounded-sm bg-amber-500"></div></div>
                        <div class="w-8 h-8 rounded-lg bg-amber-200 flex items-center justify-center"><div class="w-2 h-4 rounded-sm bg-amber-400"></div></div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Portfolio -->
    <section id="portfolio" class="py-24 bg-gray-950">
        <div class="max-w-6xl mx-auto px-6">

            <div class="text-center mb-16 portfolio-header">
                <span class="text-xs font-semibold tracking-widest text-red-500 uppercase">Our Work</span>
                <h2 class="text-3xl md:text-4xl font-bold text-white mt-2 mb-4">Portfolio</h2>
                <p class="text-gray-400 max-w-xl mx-auto">A glimpse of what we've produced for our clients across Yangon and beyond.</p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 gap-3 md:gap-4 md:[grid-auto-rows:240px]">

                <!-- Light Box — hero 2×2 -->
                <div data-pidx="0" data-pdir="zoom"
                    class="portfolio-item col-span-2 md:row-span-2 relative overflow-hidden rounded-2xl md:rounded-3xl group cursor-pointer min-h-[260px] md:min-h-0">
                    <img :src="'/images/portfolio-lightbox.jpg'" alt="Light Box"
                        class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-out" />
                    <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-black/10 to-transparent"></div>
                    <div class="absolute top-4 left-4">
                        <span class="inline-flex items-center gap-1.5 bg-white/10 backdrop-blur-md border border-white/20 rounded-full px-3 py-1 text-xs font-semibold text-white">
                            <span class="w-1.5 h-1.5 rounded-full bg-red-400 animate-pulse"></span>
                            Featured
                        </span>
                    </div>
                    <div class="absolute top-4 right-4 text-white/10 font-black text-6xl leading-none select-none">01</div>
                    <div class="absolute bottom-0 left-0 right-0 p-5 md:p-7 translate-y-1 group-hover:translate-y-0 transition-transform duration-300">
                        <p class="text-white/50 text-xs mb-1 uppercase tracking-widest">Large Format</p>
                        <h3 class="text-white font-bold text-xl md:text-2xl">Light Box</h3>
                    </div>
                </div>

                <!-- Sticker — 1×1 -->
                <div data-pidx="1" data-pdir="right"
                    class="portfolio-item relative overflow-hidden rounded-2xl group cursor-pointer min-h-[180px] md:min-h-0">
                    <img :src="'/images/portfolio-sticker.jpg'" alt="Floor & Frosted Sticker"
                        class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-700 ease-out" />
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent"></div>
                    <div class="absolute top-3 right-3 text-white/10 font-black text-3xl leading-none select-none">02</div>
                    <div class="absolute bottom-0 left-0 right-0 p-4 translate-y-1 group-hover:translate-y-0 transition-transform duration-300">
                        <p class="text-white/50 text-xs mb-0.5 uppercase tracking-widest">Stickers</p>
                        <h3 class="text-white font-semibold text-sm">Floor & Frosted Sticker</h3>
                    </div>
                </div>

                <!-- Backlit — 1×1 -->
                <div data-pidx="2" data-pdir="right"
                    class="portfolio-item relative overflow-hidden rounded-2xl group cursor-pointer min-h-[180px] md:min-h-0">
                    <img :src="'/images/portfolio-backlit.jpg'" alt="Backlit Display"
                        class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-700 ease-out" />
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent"></div>
                    <div class="absolute top-3 right-3 text-white/10 font-black text-3xl leading-none select-none">03</div>
                    <div class="absolute bottom-0 left-0 right-0 p-4 translate-y-1 group-hover:translate-y-0 transition-transform duration-300">
                        <p class="text-white/50 text-xs mb-0.5 uppercase tracking-widest">Large Format</p>
                        <h3 class="text-white font-semibold text-sm">Backlit Display</h3>
                    </div>
                </div>

                <!-- Large Format Print — 1×1 -->
                <div data-pidx="3" data-pdir="left"
                    class="portfolio-item relative overflow-hidden rounded-2xl group cursor-pointer min-h-[180px] md:min-h-0">
                    <img :src="'/images/portfolio-largeformat.jpg'" alt="Large Format Print"
                        class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-700 ease-out" />
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent"></div>
                    <div class="absolute top-3 right-3 text-white/10 font-black text-3xl leading-none select-none">04</div>
                    <div class="absolute bottom-0 left-0 right-0 p-4 translate-y-1 group-hover:translate-y-0 transition-transform duration-300">
                        <p class="text-white/50 text-xs mb-0.5 uppercase tracking-widest">Print</p>
                        <h3 class="text-white font-semibold text-sm">Large Format Print</h3>
                    </div>
                </div>

                <!-- Canvas — 1×1 -->
                <div data-pidx="4" data-pdir="up"
                    class="portfolio-item relative overflow-hidden rounded-2xl group cursor-pointer min-h-[180px] md:min-h-0">
                    <img :src="'/images/portfolio-canvas.jpg'" alt="Canvas with Frame"
                        class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-700 ease-out" />
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent"></div>
                    <div class="absolute top-3 right-3 text-white/10 font-black text-3xl leading-none select-none">05</div>
                    <div class="absolute bottom-0 left-0 right-0 p-4 translate-y-1 group-hover:translate-y-0 transition-transform duration-300">
                        <p class="text-white/50 text-xs mb-0.5 uppercase tracking-widest">Canvas</p>
                        <h3 class="text-white font-semibold text-sm">Canvas with Frame</h3>
                    </div>
                </div>

                <!-- Counter Promo Stand — 1×1 -->
                <div data-pidx="5" data-pdir="right"
                    class="portfolio-item relative overflow-hidden rounded-2xl group cursor-pointer min-h-[180px] md:min-h-0">
                    <img :src="'/images/portfolio-stand.jpg'" alt="Counter Promo Stand"
                        class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-700 ease-out" />
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent"></div>
                    <div class="absolute top-3 right-3 text-white/10 font-black text-3xl leading-none select-none">06</div>
                    <div class="absolute bottom-0 left-0 right-0 p-4 translate-y-1 group-hover:translate-y-0 transition-transform duration-300">
                        <p class="text-white/50 text-xs mb-0.5 uppercase tracking-widest">Promotional</p>
                        <h3 class="text-white font-semibold text-sm">Counter Promo Stand</h3>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Why Us -->
    <section id="why-us" class="py-24 bg-white">
        <div class="max-w-6xl mx-auto px-6">
            <div class="grid md:grid-cols-2 gap-16 items-center">
                <div>
                    <span class="text-xs font-semibold tracking-widest text-red-600 uppercase">Why Choose Us</span>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mt-2 mb-6">Printing you can count on</h2>
                    <div class="space-y-5">
                        <div v-for="point in whyUs" :key="point.title" class="flex items-start gap-4">
                            <div class="w-9 h-9 rounded-lg bg-blue-50 border border-blue-100 flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-4 h-4 text-blue-700" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 mb-0.5">{{ point.title }}</h3>
                                <p class="text-sm text-gray-500">{{ point.desc }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Address image -->
                <div class="rounded-2xl overflow-hidden shadow-lg border border-gray-100">
                    <img :src="'/images/address.jpg'" alt="Lighthouse Printing address and location" class="w-full h-auto" />
                </div>
            </div>
        </div>
    </section>

    <!-- Contact -->
    <section id="contact" class="py-24 bg-blue-900">
        <div class="max-w-6xl mx-auto px-6">
            <div class="grid md:grid-cols-2 gap-12 items-start">
                <div>
                    <span class="text-xs font-semibold tracking-widest text-red-400 uppercase">Get In Touch</span>
                    <h2 class="text-3xl md:text-4xl font-bold text-white mt-2 mb-4">Ready to print?</h2>
                    <p class="text-blue-200 mb-10">Contact us for a free quote. We respond quickly and always find the best solution for your printing needs.</p>

                    <div class="space-y-5">
                        <a v-for="contact in contacts" :key="contact.label" :href="contact.href" target="_blank"
                            class="flex items-center gap-4 group">
                            <div class="w-11 h-11 rounded-xl bg-blue-800 border border-blue-700 flex items-center justify-center shrink-0 group-hover:bg-red-600 transition-colors">
                                <component :is="contact.icon" class="w-5 h-5 text-blue-300 group-hover:text-white transition-colors" />
                            </div>
                            <div>
                                <p class="text-xs text-blue-400">{{ contact.label }}</p>
                                <p class="font-medium text-white text-sm">{{ contact.value }}</p>
                            </div>
                        </a>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-8 shadow-2xl">
                    <h3 class="font-semibold text-gray-900 mb-1">Send us a message</h3>
                    <p class="text-xs text-gray-400 mb-6">We'll get back to you as soon as possible.</p>
                    <form @submit.prevent="sendMessage" class="space-y-4" novalidate>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Name <span class="text-red-500">*</span></label>
                            <input v-model="contactForm.name" type="text" placeholder="Your name"
                                class="w-full px-3 py-2.5 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-200 bg-gray-50"
                                :class="formErrors.name ? 'border-red-400' : 'border-gray-200'" />
                            <p v-if="formErrors.name" class="mt-1 text-xs text-red-500">{{ formErrors.name }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Phone / Email <span class="text-red-500">*</span></label>
                            <input v-model="contactForm.contact" type="text" placeholder="Phone number or email"
                                class="w-full px-3 py-2.5 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-200 bg-gray-50"
                                :class="formErrors.contact ? 'border-red-400' : 'border-gray-200'" />
                            <p v-if="formErrors.contact" class="mt-1 text-xs text-red-500">{{ formErrors.contact }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Service needed <span class="text-red-500">*</span></label>
                            <select v-model="contactForm.service"
                                class="w-full px-3 py-2.5 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-200 bg-gray-50"
                                :class="formErrors.service ? 'border-red-400' : 'border-gray-200'">
                                <option value="">Select a service…</option>
                                <option v-for="s in services" :key="s.title" :value="s.title">{{ s.title }}</option>
                            </select>
                            <p v-if="formErrors.service" class="mt-1 text-xs text-red-500">{{ formErrors.service }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Message <span class="text-red-500">*</span></label>
                            <textarea v-model="contactForm.message" rows="3"
                                placeholder="Tell us about your printing requirements…"
                                class="w-full px-3 py-2.5 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-200 bg-gray-50 resize-none"
                                :class="formErrors.message ? 'border-red-400' : 'border-gray-200'"></textarea>
                            <p v-if="formErrors.message" class="mt-1 text-xs text-red-500">{{ formErrors.message }}</p>
                        </div>

                        <!-- reCAPTCHA -->
                        <div>
                            <div id="recaptcha-contact"></div>
                            <p v-if="formErrors.recaptcha_token" class="mt-1 text-xs text-red-500">{{ formErrors.recaptcha_token[0] ?? formErrors.recaptcha_token }}</p>
                        </div>

                        <button type="submit" :disabled="sendingMessage"
                            class="w-full bg-blue-700 text-white font-medium py-3 rounded-lg hover:bg-blue-800 transition-colors text-sm disabled:opacity-60 disabled:cursor-not-allowed">
                            {{ sendingMessage ? 'Sending…' : 'Send Message' }}
                        </button>

                        <!-- Success -->
                        <div v-if="messageSent" class="flex items-start gap-2.5 bg-green-50 border border-green-200 rounded-lg px-4 py-3">
                            <svg class="w-4 h-4 text-green-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                            <p class="text-sm text-green-700 font-medium">Message sent! We'll get back to you as soon as possible.</p>
                        </div>

                        <!-- Error -->
                        <div v-if="sendError" class="flex items-start gap-2.5 bg-red-50 border border-red-200 rounded-lg px-4 py-3">
                            <svg class="w-4 h-4 text-red-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                            </svg>
                            <p class="text-sm text-red-600">{{ sendError }}</p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-950 text-gray-500 py-10">
        <div class="max-w-6xl mx-auto px-6 flex flex-col md:flex-row items-center justify-between gap-6 text-sm">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-md bg-blue-700 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 21v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21m0 0h4.5V3.545M12.75 21h7.5V10.75M2.25 21h1.5m18 0h-18M2.25 21V10.75m0 0h-.375m.375 0H3.75m0 0V3.545m0 6.955h15M3.75 3.545h16.5M3.75 3.545L12 2.25l8.25 1.295" />
                    </svg>
                </div>
                <div>
                    <p class="text-white font-bold text-sm leading-none tracking-wide">LIGHTHOUSE</p>
                    <p class="text-red-500 text-xs font-semibold leading-none mt-0.5 tracking-widest">PRINTING SOLUTIONS</p>
                </div>
            </div>

            <p class="text-center text-gray-600 text-xs">
                195(A), 33rd Street, Upper Block, Kyauktada Tsp, Yangon · (+95) 9 782 275 275
            </p>

            <div class="flex items-center gap-5">
                <a href="https://www.facebook.com/lhprint/" target="_blank" rel="noopener"
                    class="text-gray-500 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    </svg>
                </a>
                <p class="text-gray-700 text-xs">© {{ new Date().getFullYear() }} Lighthouse Printing Myanmar.</p>
            </div>
        </div>
    </footer>
</template>

<script setup>
import { h, ref, computed, onMounted } from 'vue'
import axios from 'axios'
import { useLogout } from '../composables/useLogout'
import { clearAuth } from '../bootstrap'
import { useRecaptcha } from '../composables/useRecaptcha'

const { logout } = useLogout()
const { getToken, reset: resetCaptcha } = useRecaptcha('recaptcha-contact')
const isLoggedIn = ref(!!localStorage.getItem('token'))
const userName = ref('')
const heroCount = ref(0)
const messageSent = ref(false)
const contactForm = ref({ name: '', contact: '', service: '', message: '' })
const formErrors = ref({})

const userInitials = computed(() =>
    userName.value.split(' ').map(w => w[0]).slice(0, 2).join('').toUpperCase() || '?'
)

onMounted(async () => {
    if (isLoggedIn.value) {
        try {
            const { data } = await axios.get('/api/me')
            userName.value = data.name
        } catch {
            clearAuth()
            isLoggedIn.value = false
        }
    }

    // Header fade-in
    const headerEl = document.querySelector('.bento-header')
    if (headerEl) {
        const ho = new IntersectionObserver(([e]) => {
            if (e.isIntersecting) { headerEl.classList.add('bento-header-visible'); ho.disconnect() }
        }, { threshold: 0.4 })
        ho.observe(headerEl)
    }

    // Bento card entrance + counter
    const bentoObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (!entry.isIntersecting) return
            const i = parseInt(entry.target.dataset.bento ?? '0')
            setTimeout(() => {
                entry.target.classList.add('visible')
                if (i === 0) {
                    const target = 500, dur = 1800, t0 = performance.now()
                    const tick = (t) => {
                        const p = Math.min((t - t0) / dur, 1)
                        heroCount.value = Math.round((1 - Math.pow(1 - p, 3)) * target)
                        if (p < 1) requestAnimationFrame(tick)
                    }
                    requestAnimationFrame(tick)
                }
            }, i * 80)
            bentoObserver.unobserve(entry.target)
        })
    }, { threshold: 0.1 })
    document.querySelectorAll('.bento-item').forEach(el => bentoObserver.observe(el))

    // Portfolio header
    const portHeader = document.querySelector('.portfolio-header')
    if (portHeader) {
        const pho = new IntersectionObserver(([e]) => {
            if (e.isIntersecting) { portHeader.classList.add('portfolio-header-visible'); pho.disconnect() }
        }, { threshold: 0.4 })
        pho.observe(portHeader)
    }

    // Portfolio items — directional entrance, staggered
    const portfolioObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (!entry.isIntersecting) return
            const i = parseInt(entry.target.dataset.pidx ?? '0')
            setTimeout(() => entry.target.classList.add('visible'), i * 110)
            portfolioObserver.unobserve(entry.target)
        })
    }, { threshold: 0.1 })
    document.querySelectorAll('.portfolio-item').forEach(el => portfolioObserver.observe(el))
})

const sendingMessage = ref(false)
const sendError = ref('')

function validateForm() {
    const errs = {}
    if (!contactForm.value.name.trim())    errs.name    = 'Name is required.'
    if (!contactForm.value.contact.trim()) errs.contact = 'Phone or email is required.'
    if (!contactForm.value.service)        errs.service = 'Please select a service.'
    if (!contactForm.value.message.trim()) errs.message = 'Message is required.'
    return errs
}

async function sendMessage() {
    sendError.value = ''
    messageSent.value = false
    formErrors.value = validateForm()
    if (Object.keys(formErrors.value).length) return

    sendingMessage.value = true
    try {
        await axios.post('/api/contact', { ...contactForm.value, recaptcha_token: getToken() })
        messageSent.value = true
        contactForm.value = { name: '', contact: '', service: '', message: '' }
        resetCaptcha()
        setTimeout(() => messageSent.value = false, 6000)
    } catch (e) {
        if (e?.response?.status === 429) {
            sendError.value = 'Too many attempts. Please wait a minute and try again.'
        } else if (e?.response?.status === 422) {
            const errs = e.response.data.errors ?? {}
            formErrors.value = { ...formErrors.value, ...errs }
        } else {
            sendError.value = 'Failed to send email. Please try again or call us directly.'
        }
        resetCaptcha()
    } finally {
        sendingMessage.value = false
    }
}

const stats = [
    { value: '9+',    label: 'Print Services',     sub: 'All in one place' },
    { value: '100%',  label: 'Quality Assured',     sub: 'Every job checked' },
    { value: 'Fast',  label: 'Turnaround',          sub: 'Rush orders welcome' },
    { value: 'Best',  label: 'Budget Effective',    sub: 'Competitive pricing' },
]

// ── Icons ──────────────────────────────────────────────────────────────────
const icon = (d) => ({ render: () => h('svg', { fill: 'none', stroke: 'currentColor', 'stroke-width': '2', viewBox: '0 0 24 24' }, [h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d })]) })

const IconLarge   = icon('M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3 1 3m0 0 .5 1.5m-.5-1.5h-9.5m0 0-.5 1.5M9 11.25v1.5M12 9v3.75m3-6v6')
const IconOffset  = icon('M6.429 9.75L2.25 12l4.179 2.25m0-4.5l5.571 3 5.571-3m-11.142 0L2.25 7.5 12 2.25l9.75 5.25-4.179 2.25m0 0L21.75 12l-4.179 2.25m0 0l4.179 2.25L12 21.75 2.25 16.5l4.179-2.25m11.142 0l-5.571 3-5.571-3')
const IconBrand   = icon('M9.53 16.122a3 3 0 00-5.78 1.128 2.25 2.25 0 01-2.4 2.245 4.5 4.5 0 008.4-2.245c0-.399-.078-.78-.22-1.128zm0 0a15.998 15.998 0 003.388-1.62m-5.043-.025a15.994 15.994 0 011.622-3.395m3.42 3.42a15.995 15.995 0 004.764-4.648l3.876-5.814a1.151 1.151 0 00-1.597-1.597L14.146 6.32a15.996 15.996 0 00-4.649 4.763m3.42 3.42a6.776 6.776 0 00-3.42-3.42')
const IconColor   = icon('M4.098 19.902a3.75 3.75 0 005.304 0l6.401-6.402M6.75 21A3.75 3.75 0 013 17.25V4.125C3 3.504 3.504 3 4.125 3h5.25c.621 0 1.125.504 1.125 1.125v4.072M6.75 21a3.75 3.75 0 003.75-3.75V8.197M6.75 21h13.125c.621 0 1.125-.504 1.125-1.125v-5.25c0-.621-.504-1.125-1.125-1.125h-4.072M10.5 8.197l2.88-2.88c.438-.439 1.15-.439 1.59 0l3.712 3.713c.44.44.44 1.152 0 1.59l-2.879 2.88M6.75 17.25h.008v.008H6.75v-.008z')
const IconSticker = icon('M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L9.568 3zM6 6h.008v.008H6V6z')
const IconPromo   = icon('M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z')
const IconScreen  = icon('M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15a9.065 9.065 0 00-6.23-.693L5 14.5m14.8.8 1.402 1.402c1.232 1.232.65 3.318-1.067 3.611A48.309 48.309 0 0112 21c-2.773 0-5.491-.235-8.135-.687-1.718-.293-2.3-2.379-1.067-3.61L5 14.5')
const IconCard    = icon('M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z')
const IconPackage = icon('M7.875 14.25l1.214 1.942a2.25 2.25 0 001.908 1.058h2.006c.776 0 1.497-.4 1.908-1.058l1.214-1.942M2.41 9h4.636a2.25 2.25 0 011.872 1.002l.164.246a2.25 2.25 0 001.872 1.002h2.092a2.25 2.25 0 001.872-1.002l.164-.246A2.25 2.25 0 0116.954 9h4.636M2.41 9A2.25 2.25 0 002.25 9.75v.259a2.25 2.25 0 00.8 1.737L12 19.5l8.95-7.754a2.25 2.25 0 00.8-1.737V9.75A2.25 2.25 0 0019.59 9M2.41 9H21.59')

const IconPhone = icon('M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z')
const IconPhone2 = icon('M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z')
const IconMail   = icon('M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75')
const IconMap    = icon('M15 10.5a3 3 0 11-6 0 3 3 0 016 0zM19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z')
const IconFB = {
    render: () => h('svg', { fill: 'currentColor', viewBox: '0 0 24 24', class: 'w-5 h-5' },
        [h('path', { d: 'M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z' })])
}

const services = [
    { icon: IconLarge,   title: 'Large Format Printing',   color: 'bg-blue-600',   description: 'Banners, light boxes, backlit prints, canvas, PP board, and large display materials.' },
    { icon: IconOffset,  title: 'Offset Printing',         color: 'bg-slate-700',  description: 'High-volume, consistent colour accuracy for books, catalogues, and corporate materials.' },
    { icon: IconBrand,   title: 'Branding',                color: 'bg-red-600',    description: 'Full branding packages — logos, stationery, and branded print collateral for your business.' },
    { icon: IconColor,   title: 'Color Print',             color: 'bg-purple-600', description: 'Vivid full-colour digital prints for brochures, flyers, posters, and presentations.' },
    { icon: IconSticker, title: 'Stickers & Labels',       color: 'bg-green-600',  description: 'Floor stickers, frosted stickers, product labels in any shape and material.' },
    { icon: IconPromo,   title: 'Promotional Products',    color: 'bg-orange-500', description: 'Counter stands, display materials, and branded promotional items for events and marketing.' },
    { icon: IconScreen,  title: 'Silk Screen Printing',    color: 'bg-indigo-600', description: 'Durable screen printing on fabric, apparel, and promotional items.' },
    { icon: IconCard,    title: 'Business Cards',          color: 'bg-blue-500',   description: 'Matte, gloss, spot UV — professional cards that make the right first impression.' },
    { icon: IconPackage, title: 'Packaging',               color: 'bg-amber-600',  description: 'Branded boxes, bags, and custom packaging for retail and gifting purposes.' },
]

const portfolio = [
    { src: '/images/portfolio-lightbox.jpg',    label: 'Light Box' },
    { src: '/images/portfolio-sticker.jpg',     label: 'Floor & Frosted Sticker' },
    { src: '/images/portfolio-largeformat.jpg', label: 'Large Format Print' },
    { src: '/images/portfolio-backlit.jpg',     label: 'Backlit Display' },
    { src: '/images/portfolio-canvas.jpg',      label: 'Canvas with Frame' },
    { src: '/images/portfolio-stand.jpg',       label: 'Counter Promo Stand' },
]

const whyUs = [
    { title: 'Widest Range of Services',  desc: 'From offset to silk screen — all printing needs handled under one roof.' },
    { title: 'Budget Effective Pricing',  desc: 'Competitive rates without cutting corners. We find the best option for your budget.' },
    { title: 'Quality Guaranteed',        desc: 'Every job is quality-checked before delivery. We stand behind every print.' },
    { title: 'Fast Turnaround',           desc: 'We understand deadlines. Rush jobs handled with the same care as regular orders.' },
    { title: 'Expert Consultation',       desc: 'Not sure which option suits you? Our team will guide you to the right solution.' },
]

const contacts = [
    { icon: IconPhone,  label: 'Phone',    value: '(+95) 9 782 275 275',        href: 'tel:+959782275275' },
    { icon: IconPhone2, label: 'Phone 2',  value: '09 446 447 028',             href: 'tel:+95944644702' },
    { icon: IconMail,   label: 'Email',    value: 'info@lighthouse-print.com',  href: 'mailto:info@lighthouse-print.com' },
    { icon: IconMap,    label: 'Address',  value: '195(A), 33rd Street, Upper Block, Kyauktada Tsp, Yangon', href: 'https://maps.google.com/?q=Kyauktada+Yangon+Myanmar' },
    { icon: IconFB,     label: 'Facebook', value: 'facebook.com/lhprint',       href: 'https://www.facebook.com/lhprint/' },
]
</script>

<style scoped>
/* ── Bento header ─────────────────────────────────────────── */
.bento-header {
    opacity: 0;
    transform: translateY(20px);
    transition: opacity 0.7s ease, transform 0.7s ease;
}
.bento-header-visible {
    opacity: 1;
    transform: translateY(0);
}

/* ── Bento card entrance ──────────────────────────────────── */
.bento-item {
    opacity: 0;
    transform: translateY(28px);
    transition: opacity 0.55s ease, transform 0.55s ease, box-shadow 0.3s ease;
}
.bento-item.visible {
    opacity: 1;
    transform: translateY(0);
}

/* ── Hero shimmer sweep ───────────────────────────────────── */
.bento-shimmer {
    position: absolute;
    inset: 0;
    overflow: hidden;
    border-radius: inherit;
    pointer-events: none;
}
.bento-shimmer::after {
    content: '';
    position: absolute;
    top: -50%;
    left: -75%;
    width: 50%;
    height: 200%;
    background: linear-gradient(100deg, transparent 20%, rgba(255,255,255,0.07) 50%, transparent 80%);
    transform: skewX(-15deg);
    animation: bento-shine 3.5s ease-in-out infinite;
}
@keyframes bento-shine {
    0%   { left: -75%; }
    60%, 100% { left: 130%; }
}

/* ── Portfolio header ─────────────────────────────────────── */
.portfolio-header {
    opacity: 0;
    transform: translateY(20px);
    transition: opacity 0.7s ease, transform 0.7s ease;
}
.portfolio-header-visible {
    opacity: 1;
    transform: translateY(0);
}

/* ── Portfolio card entrance (directional) ────────────────── */
.portfolio-item {
    opacity: 0;
    transition: opacity 0.65s cubic-bezier(0.22, 1, 0.36, 1),
                transform 0.65s cubic-bezier(0.22, 1, 0.36, 1);
}
.portfolio-item[data-pdir="zoom"]  { transform: scale(0.92); }
.portfolio-item[data-pdir="left"]  { transform: translateX(-48px); }
.portfolio-item[data-pdir="right"] { transform: translateX(48px); }
.portfolio-item[data-pdir="up"]    { transform: translateY(36px); }
.portfolio-item.visible {
    opacity: 1;
    transform: none;
}
</style>
