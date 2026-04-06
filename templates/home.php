<main class="min-h-screen bg-gradient-to-b from-blue-50 to-white">
    <section class="mx-auto max-w-5xl px-6 py-16">
        <div class="mb-10 text-center">
            <span class="inline-flex items-center rounded-full bg-blue-100 px-4 py-1 text-sm font-semibold text-blue-700">Immobilier Bordeaux</span>
            <h1 class="mt-4 text-4xl font-extrabold tracking-tight text-blue-900">Obtenez une estimation fiable de votre bien</h1>
            <p class="mt-3 text-lg text-slate-600">Remplissez ce formulaire pour recevoir une estimation rapide et personnalisée.</p>
        </div>

        <div class="mx-auto max-w-3xl rounded-2xl bg-white p-8 shadow-xl ring-1 ring-slate-200">
            <form method="post" action="/" class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <label for="prenom" class="mb-2 block text-sm font-medium text-slate-700">Prénom</label>
                    <input id="prenom" name="prenom" type="text" required class="w-full rounded-lg border border-slate-300 px-4 py-3 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200">
                </div>
                <div>
                    <label for="email" class="mb-2 block text-sm font-medium text-slate-700">Email</label>
                    <input id="email" name="email" type="email" required class="w-full rounded-lg border border-slate-300 px-4 py-3 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200">
                </div>
                <div>
                    <label for="type_bien" class="mb-2 block text-sm font-medium text-slate-700">Type de bien</label>
                    <select id="type_bien" name="type_bien" required class="w-full rounded-lg border border-slate-300 px-4 py-3 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200">
                        <option value="">Sélectionnez</option>
                        <option value="appartement">Appartement</option>
                        <option value="maison">Maison</option>
                        <option value="terrain">Terrain</option>
                        <option value="commerce">Commerce</option>
                        <option value="immeuble">Immeuble</option>
                    </select>
                </div>
                <div>
                    <label for="surface" class="mb-2 block text-sm font-medium text-slate-700">Surface (m²)</label>
                    <input id="surface" name="surface" type="number" min="1" required class="w-full rounded-lg border border-slate-300 px-4 py-3 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200">
                </div>
                <div class="md:col-span-2">
                    <label for="adresse" class="mb-2 block text-sm font-medium text-slate-700">Adresse</label>
                    <input id="adresse" name="adresse" type="text" required class="w-full rounded-lg border border-slate-300 px-4 py-3 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200">
                </div>
                <div class="md:col-span-2">
                    <label for="ville" class="mb-2 block text-sm font-medium text-slate-700">Ville</label>
                    <input id="ville" name="ville" type="text" value="Bordeaux" required class="w-full rounded-lg border border-slate-300 px-4 py-3 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200">
                </div>
                <div class="md:col-span-2">
                    <button type="submit" class="w-full rounded-lg bg-blue-600 px-5 py-3 text-base font-semibold text-white transition hover:bg-blue-700">Recevoir mon estimation</button>
                </div>
            </form>
        </div>
    </section>
</main>
