<?php

declare(strict_types=1);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estimation immobilière instantanée à Bordeaux</title>
    <meta name="description" content="Estimez gratuitement votre bien immobilier à Bordeaux en 30 secondes.">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-white text-slate-900 antialiased">
    <main>
        <section id="hero" class="bg-gradient-to-br from-blue-900 via-blue-800 to-indigo-700 text-white">
            <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 md:py-20 lg:px-8">
                <div class="mx-auto max-w-4xl text-center">
                    <p class="mx-auto inline-flex items-center rounded-full bg-white/15 px-4 py-2 text-sm font-semibold backdrop-blur">
                        ✨ Estimation gratuite en 30 secondes
                    </p>
                    <h1 class="mt-6 text-4xl font-extrabold leading-tight md:text-5xl">
                        Combien vaut votre bien immobilier à Bordeaux ?
                    </h1>
                    <p class="mt-4 text-base text-blue-100 md:text-lg">
                        Obtenez une estimation instantanée basée sur les données du marché local.
                    </p>
                </div>

                <form id="estimation-form" class="mt-10 rounded-2xl bg-white/10 p-3 backdrop-blur-sm">
                    <div class="flex flex-col gap-3 lg:flex-row lg:items-stretch">
                        <div class="w-full lg:flex-1 lg:pr-3 lg:border-r lg:border-white/20">
                            <label for="type_bien" class="mb-1 block text-sm font-medium text-blue-100">🏠 Type de bien</label>
                            <select id="type_bien" name="type_bien" required class="w-full rounded-xl border-0 bg-gray-50 px-4 py-4 text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-300">
                                <option value="">Choisir</option>
                                <option value="Appartement">Appartement</option>
                                <option value="Maison">Maison</option>
                                <option value="Terrain">Terrain</option>
                                <option value="Local commercial">Local commercial</option>
                            </select>
                        </div>

                        <div class="w-full lg:flex-1 lg:px-3 lg:border-r lg:border-white/20">
                            <label for="ville" class="mb-1 block text-sm font-medium text-blue-100">📍 Ville</label>
                            <select id="ville" name="ville" required class="w-full rounded-xl border-0 bg-gray-50 px-4 py-4 text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-300">
                                <option value="">Choisir</option>
                                <option value="Bordeaux">Bordeaux</option>
                                <option value="Mérignac">Mérignac</option>
                                <option value="Pessac">Pessac</option>
                                <option value="Talence">Talence</option>
                                <option value="Bègles">Bègles</option>
                                <option value="Villenave-d'Ornon">Villenave-d'Ornon</option>
                                <option value="Gradignan">Gradignan</option>
                                <option value="Le Bouscat">Le Bouscat</option>
                                <option value="Bruges">Bruges</option>
                                <option value="Cenon">Cenon</option>
                            </select>
                        </div>

                        <div class="w-full lg:flex-1 lg:px-3 lg:border-r lg:border-white/20">
                            <label for="surface_tranche" class="mb-1 block text-sm font-medium text-blue-100">📏 Surface</label>
                            <select id="surface_tranche" name="surface_tranche" required class="w-full rounded-xl border-0 bg-gray-50 px-4 py-4 text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-300">
                                <option value="">Choisir</option>
                                <option value="lt30">Moins de 30 m²</option>
                                <option value="30_50">30-50 m²</option>
                                <option value="50_80">50-80 m²</option>
                                <option value="80_120">80-120 m²</option>
                                <option value="120_200">120-200 m²</option>
                                <option value="gt200">Plus de 200 m²</option>
                            </select>
                        </div>

                        <div class="w-full lg:flex-1 lg:px-3">
                            <label for="budget_tranche" class="mb-1 block text-sm font-medium text-blue-100">💶 Budget estimé</label>
                            <select id="budget_tranche" name="budget_tranche" required class="w-full rounded-xl border-0 bg-gray-50 px-4 py-4 text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-300">
                                <option value="">Choisir</option>
                                <option value="lt150">&lt; 150 000 €</option>
                                <option value="150_300">150k-300k €</option>
                                <option value="300_500">300k-500k €</option>
                                <option value="gt500">&gt; 500 000 €</option>
                            </select>
                        </div>

                        <div class="w-full lg:w-auto lg:pl-3">
                            <label class="mb-1 hidden text-sm font-medium text-blue-100 lg:block">&nbsp;</label>
                            <button type="submit" class="h-[56px] w-full rounded-xl bg-orange-500 px-8 text-base font-bold text-white transition hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-orange-300 lg:w-auto">
                                Estimer →
                            </button>
                        </div>
                    </div>
                    <p id="form-feedback" class="mt-3 hidden text-sm font-medium text-red-200"></p>
                </form>
            </div>
        </section>

        <section id="result-section" class="pointer-events-none max-h-0 -translate-y-4 overflow-hidden bg-gray-100 px-4 py-0 opacity-0 transition-all duration-500 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-lg rounded-2xl bg-white p-6 shadow-2xl md:p-8">
                <h2 class="text-center text-2xl font-bold text-slate-900">Estimation de votre bien</h2>
                <p id="result-recap" class="mt-2 text-center text-sm font-medium text-slate-500"></p>
                <p id="result-range" class="mt-6 text-center text-4xl font-extrabold text-green-600"></p>
                <p id="result-price-m2" class="mt-2 text-center text-sm text-slate-500"></p>

                <hr class="my-6 border-slate-200">

                <div id="result-workflow" class="space-y-4">
                    <p class="text-center text-sm text-slate-700">Pour affiner cette estimation, parlez à un conseiller.</p>
                    <button id="start-bant" type="button" class="w-full rounded-xl bg-gradient-to-r from-blue-700 to-indigo-600 px-4 py-3 font-bold text-white transition hover:from-blue-800 hover:to-indigo-700">
                        Affiner avec un conseiller
                    </button>

                    <form id="bant-form" class="pointer-events-none max-h-0 -translate-y-2 space-y-4 overflow-hidden opacity-0 transition-all duration-500">
                        <div>
                            <p class="mb-2 text-sm font-semibold text-slate-700">Quel est votre projet ?</p>
                            <div id="projet-pills" class="grid grid-cols-1 gap-2 sm:grid-cols-3">
                                <label class="cursor-pointer rounded-xl border border-blue-200 px-3 py-2 text-center text-sm font-semibold text-blue-700 transition hover:border-blue-500 hover:bg-blue-50 has-[:checked]:border-blue-600 has-[:checked]:bg-blue-600 has-[:checked]:text-white">
                                    <input type="radio" name="projet" value="Vendre mon bien" class="sr-only" required>
                                    Vendre mon bien
                                </label>
                                <label class="cursor-pointer rounded-xl border border-blue-200 px-3 py-2 text-center text-sm font-semibold text-blue-700 transition hover:border-blue-500 hover:bg-blue-50 has-[:checked]:border-blue-600 has-[:checked]:bg-blue-600 has-[:checked]:text-white">
                                    <input type="radio" name="projet" value="Acheter un bien" class="sr-only" required>
                                    Acheter un bien
                                </label>
                                <label class="cursor-pointer rounded-xl border border-blue-200 px-3 py-2 text-center text-sm font-semibold text-blue-700 transition hover:border-blue-500 hover:bg-blue-50 has-[:checked]:border-blue-600 has-[:checked]:bg-blue-600 has-[:checked]:text-white">
                                    <input type="radio" name="projet" value="Investir dans l'immobilier" class="sr-only" required>
                                    Investir
                                </label>
                            </div>
                        </div>

                        <div>
                            <label for="decisionnaire" class="mb-1 block text-sm font-semibold text-slate-700">Êtes-vous le décisionnaire ?</label>
                            <select id="decisionnaire" name="decisionnaire" required class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:outline-none">
                                <option value="">Choisir</option>
                                <option value="Oui, seul(e)">Oui, seul(e)</option>
                                <option value="Oui, en couple / à plusieurs">Oui, en couple / à plusieurs</option>
                                <option value="Non, je me renseigne pour quelqu'un">Non, je me renseigne pour quelqu'un</option>
                            </select>
                        </div>

                        <div>
                            <label for="budget_bant" class="mb-1 block text-sm font-semibold text-slate-700">Quel est votre budget ou prix de vente souhaité ?</label>
                            <select id="budget_bant" name="budget_bant" required class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:outline-none">
                                <option value="">Choisir</option>
                                <option value="Moins de 150 000 €">Moins de 150 000 €</option>
                                <option value="150 000 € - 300 000 €">150 000 € - 300 000 €</option>
                                <option value="300 000 € - 500 000 €">300 000 € - 500 000 €</option>
                                <option value="500 000 € - 800 000 €">500 000 € - 800 000 €</option>
                                <option value="Plus de 800 000 €">Plus de 800 000 €</option>
                            </select>
                        </div>

                        <div>
                            <label for="delai" class="mb-1 block text-sm font-semibold text-slate-700">Quel est votre délai ?</label>
                            <select id="delai" name="delai" required class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:outline-none">
                                <option value="">Choisir</option>
                                <option value="Urgent (moins de 3 mois)">Urgent (moins de 3 mois)</option>
                                <option value="Dans les 6 mois">Dans les 6 mois</option>
                                <option value="Dans l'année">Dans l'année</option>
                                <option value="Pas de délai précis">Pas de délai précis</option>
                            </select>
                        </div>

                        <div>
                            <label for="raison" class="mb-1 block text-sm font-semibold text-slate-700">Quelle est la raison principale ?</label>
                            <select id="raison" name="raison" required class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:outline-none">
                                <option value="">Choisir</option>
                                <option value="Déménagement / mutation">Déménagement / mutation</option>
                                <option value="Séparation / divorce">Séparation / divorce</option>
                                <option value="Succession / héritage">Succession / héritage</option>
                                <option value="Investissement locatif">Investissement locatif</option>
                                <option value="Résidence principale">Résidence principale</option>
                                <option value="Agrandissement famille">Agrandissement famille</option>
                                <option value="Autre">Autre</option>
                            </select>
                        </div>

                        <button id="continue-contact" type="button" class="w-full rounded-xl bg-slate-900 px-4 py-3 font-semibold text-white transition hover:bg-slate-800">
                            Continuer →
                        </button>
                    </form>

                    <form id="contact-form" class="pointer-events-none max-h-0 -translate-y-2 space-y-3 overflow-hidden opacity-0 transition-all duration-500">
                        <input id="prenom" name="prenom" type="text" placeholder="Prénom" required class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:outline-none">
                        <input id="telephone" name="telephone" type="tel" placeholder="Téléphone" required class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:outline-none">
                        <input id="email" name="email" type="email" placeholder="Email" required class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:outline-none">
                        <button id="contact-submit" type="submit" class="w-full rounded-xl bg-gradient-to-r from-blue-700 to-blue-500 px-4 py-3 font-bold text-white transition hover:from-blue-800 hover:to-blue-600">
                            Me faire rappeler gratuitement →
                        </button>
                    </form>

                    <p id="contact-feedback" class="hidden rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-semibold text-green-700"></p>
                </div>

                <button id="new-estimation" type="button" class="mt-4 w-full rounded-xl border border-slate-300 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                    ← Nouvelle estimation
                </button>
            </div>
        </section>

        <section class="bg-gray-50 px-4 py-16 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-6xl">
                <h2 class="text-center text-3xl font-bold text-slate-900">Comment ça marche</h2>
                <div class="mt-10 grid gap-6 md:grid-cols-3">
                    <article class="rounded-2xl bg-white p-6 shadow-sm">
                        <div class="mb-4 flex h-10 w-10 items-center justify-center rounded-full border-2 border-blue-600 text-sm font-bold text-blue-600">1</div>
                        <p class="text-2xl">📝</p>
                        <h3 class="mt-3 text-lg font-semibold">Décrivez votre bien</h3>
                        <p class="mt-2 text-sm text-slate-600">Sélectionnez le type de bien, la ville, la surface et votre budget estimé.</p>
                    </article>
                    <article class="rounded-2xl bg-white p-6 shadow-sm">
                        <div class="mb-4 flex h-10 w-10 items-center justify-center rounded-full border-2 border-blue-600 text-sm font-bold text-blue-600">2</div>
                        <p class="text-2xl">⚡</p>
                        <h3 class="mt-3 text-lg font-semibold">Estimation instantanée</h3>
                        <p class="mt-2 text-sm text-slate-600">Notre algorithme calcule immédiatement une fourchette cohérente avec le marché local.</p>
                    </article>
                    <article class="rounded-2xl bg-white p-6 shadow-sm">
                        <div class="mb-4 flex h-10 w-10 items-center justify-center rounded-full border-2 border-blue-600 text-sm font-bold text-blue-600">3</div>
                        <p class="text-2xl">📞</p>
                        <h3 class="mt-3 text-lg font-semibold">Un expert vous rappelle</h3>
                        <p class="mt-2 text-sm text-slate-600">Laissez vos coordonnées pour obtenir une estimation affinée par un conseiller local.</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="bg-white px-4 py-16 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-5xl text-center">
                <h2 class="text-3xl font-bold text-slate-900">Déjà 500+ estimations à Bordeaux</h2>
                <div class="mt-8 grid gap-6 md:grid-cols-3">
                    <blockquote class="rounded-2xl border border-slate-200 p-5 text-left">
                        <p class="text-amber-500">★★★★★</p>
                        <p class="mt-3 text-sm text-slate-600">"Très rapide et cohérent avec les prix que j'avais repérés dans mon quartier."</p>
                        <footer class="mt-3 text-xs font-semibold text-slate-500">— Claire, Bordeaux Centre</footer>
                    </blockquote>
                    <blockquote class="rounded-2xl border border-slate-200 p-5 text-left">
                        <p class="text-amber-500">★★★★★</p>
                        <p class="mt-3 text-sm text-slate-600">"J'ai eu une fourchette réaliste en moins d'une minute, super pratique."</p>
                        <footer class="mt-3 text-xs font-semibold text-slate-500">— Thomas, Mérignac</footer>
                    </blockquote>
                    <blockquote class="rounded-2xl border border-slate-200 p-5 text-left">
                        <p class="text-amber-500">★★★★★</p>
                        <p class="mt-3 text-sm text-slate-600">"Interface simple et claire, parfait pour une première idée de prix."</p>
                        <footer class="mt-3 text-xs font-semibold text-slate-500">— Nadia, Pessac</footer>
                    </blockquote>
                </div>
            </div>
        </section>
    </main>

    <footer class="border-t border-slate-200 bg-white px-4 py-6 text-center text-xs text-slate-400 sm:px-6 lg:px-8">
        © 2025 · <a href="/pages/mentions-legales.php" class="hover:text-slate-600">Mentions légales</a> ·
        <a href="/pages/politique-confidentialite.php" class="hover:text-slate-600">Politique de confidentialité</a>
    </footer>

    <script>
        const form = document.getElementById('estimation-form');
        const feedback = document.getElementById('form-feedback');
        const resultSection = document.getElementById('result-section');
        const recap = document.getElementById('result-recap');
        const range = document.getElementById('result-range');
        const priceM2 = document.getElementById('result-price-m2');
        const newEstimationBtn = document.getElementById('new-estimation');
        const startBantBtn = document.getElementById('start-bant');
        const bantForm = document.getElementById('bant-form');
        const continueContactBtn = document.getElementById('continue-contact');
        const contactForm = document.getElementById('contact-form');
        const contactFeedback = document.getElementById('contact-feedback');
        const contactSubmit = document.getElementById('contact-submit');
        const projetPills = document.getElementById('projet-pills');

        let latestEstimation = null;

        const surfaceLabels = {
            lt30: 'Moins de 30 m²',
            '30_50': '30-50 m²',
            '50_80': '50-80 m²',
            '80_120': '80-120 m²',
            '120_200': '120-200 m²',
            gt200: 'Plus de 200 m²'
        };

        const formatPrice = (value) => new Intl.NumberFormat('fr-FR').format(Math.round(value)) + ' €';
        const showStep = (element) => {
            element.classList.remove('pointer-events-none', 'max-h-0', '-translate-y-2', 'opacity-0');
            element.classList.add('pointer-events-auto', 'max-h-[1200px]', 'translate-y-0', 'opacity-100');
        };
        const hideStep = (element) => {
            element.classList.add('pointer-events-none', 'max-h-0', '-translate-y-2', 'opacity-0');
            element.classList.remove('pointer-events-auto', 'max-h-[1200px]', 'translate-y-0', 'opacity-100');
        };

        projetPills.addEventListener('change', () => {
            [...projetPills.querySelectorAll('label')].forEach((label) => {
                const input = label.querySelector('input[type="radio"]');
                label.classList.toggle('ring-2', input.checked);
                label.classList.toggle('ring-blue-200', input.checked);
            });
        });

        form.addEventListener('submit', async (event) => {
            event.preventDefault();
            feedback.classList.add('hidden');
            feedback.textContent = '';
            contactFeedback.classList.add('hidden');
            contactFeedback.textContent = '';
            hideStep(bantForm);
            hideStep(contactForm);
            startBantBtn.classList.remove('hidden');

            const submitButton = form.querySelector('button[type="submit"]');
            const buttonText = submitButton.textContent;
            submitButton.disabled = true;
            submitButton.textContent = 'Calcul en cours...';

            try {
                const formData = new FormData(form);
                const response = await fetch('/api/estimation.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (!response.ok || !data.success) {
                    throw new Error(data.message || 'Une erreur est survenue.');
                }

                const selectedType = formData.get('type_bien');
                const selectedVille = formData.get('ville');
                const selectedSurface = surfaceLabels[formData.get('surface_tranche')] || '';

                recap.textContent = `${selectedType} · ${selectedVille} · ${selectedSurface}`;
                range.textContent = `${formatPrice(data.estimation_basse)} — ${formatPrice(data.estimation_haute)}`;
                priceM2.textContent = `Prix moyen au m² : ${formatPrice(data.prix_m2)}`;
                latestEstimation = {
                    type_bien: selectedType,
                    ville: selectedVille,
                    surface_tranche: formData.get('surface_tranche'),
                    estimation_basse: data.estimation_basse,
                    estimation_haute: data.estimation_haute
                };

                resultSection.classList.remove('pointer-events-none', 'max-h-0', '-translate-y-4', 'opacity-0', 'py-0');
                resultSection.classList.add('max-h-[1200px]', 'translate-y-0', 'opacity-100', 'py-12');
                resultSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
            } catch (error) {
                feedback.textContent = error.message || 'Impossible de calculer l\'estimation pour le moment.';
                feedback.classList.remove('hidden');
            } finally {
                submitButton.disabled = false;
                submitButton.textContent = buttonText;
            }
        });

        startBantBtn.addEventListener('click', () => {
            showStep(bantForm);
            startBantBtn.classList.add('hidden');
        });

        continueContactBtn.addEventListener('click', () => {
            if (!bantForm.reportValidity()) {
                return;
            }
            showStep(contactForm);
            contactForm.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        });

        contactForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            if (!latestEstimation) {
                contactFeedback.className = 'rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-700';
                contactFeedback.textContent = 'Veuillez réaliser une estimation avant de continuer.';
                return;
            }

            const buttonText = contactSubmit.textContent;
            contactSubmit.disabled = true;
            contactSubmit.textContent = 'Envoi en cours...';

            try {
                const payload = new FormData();
                const bantData = new FormData(bantForm);
                const contactData = new FormData(contactForm);
                [...bantData.entries(), ...contactData.entries()].forEach(([key, value]) => payload.append(key, value));
                Object.entries(latestEstimation).forEach(([key, value]) => payload.append(key, String(value)));

                const response = await fetch('/api/contact.php', {
                    method: 'POST',
                    body: payload
                });
                const data = await response.json();

                if (!response.ok || !data.success) {
                    throw new Error(data.message || 'Impossible d\'envoyer votre demande.');
                }

                const prenom = (contactData.get('prenom') || '').toString().trim();
                contactFeedback.className = 'rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-semibold text-green-700 transition-all duration-500';
                contactFeedback.textContent = `✅ Merci ${prenom} ! Un conseiller vous rappelle sous 24h.`;
                contactFeedback.classList.remove('hidden');
                contactFeedback.animate(
                    [{ transform: 'scale(0.96)', opacity: 0 }, { transform: 'scale(1)', opacity: 1 }],
                    { duration: 280, easing: 'ease-out' }
                );
                contactForm.reset();
            } catch (error) {
                contactFeedback.className = 'rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700';
                contactFeedback.textContent = error.message || 'Le service est momentanément indisponible.';
                contactFeedback.classList.remove('hidden');
            } finally {
                contactSubmit.disabled = false;
                contactSubmit.textContent = buttonText;
            }
        });

        newEstimationBtn.addEventListener('click', () => {
            resultSection.classList.add('pointer-events-none', 'max-h-0', '-translate-y-4', 'opacity-0', 'py-0');
            resultSection.classList.remove('max-h-[1200px]', 'translate-y-0', 'opacity-100', 'py-12');
            hideStep(bantForm);
            hideStep(contactForm);
            startBantBtn.classList.remove('hidden');
            bantForm.reset();
            contactForm.reset();
            contactFeedback.classList.add('hidden');
            contactFeedback.textContent = '';
            form.scrollIntoView({ behavior: 'smooth', block: 'center' });
        });
    </script>
</body>
</html>
