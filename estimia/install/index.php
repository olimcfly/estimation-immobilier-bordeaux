<?php

declare(strict_types=1);

$lockPath = dirname(__DIR__) . '/installed.lock';
if (is_file($lockPath)) {
    $siteUrl = '../';
    echo '<!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>EstimIA installé</title></head><body style="font-family:Inter,sans-serif;padding:40px">';
    echo '<h1>EstimIA est déjà installé.</h1><p><a href="' . htmlspecialchars($siteUrl, ENT_QUOTES, 'UTF-8') . '">Aller vers le site</a></p>';
    echo '</body></html>';
    die;
}

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (empty($_SESSION['install_csrf']) || !is_string($_SESSION['install_csrf'])) {
    $_SESSION['install_csrf'] = bin2hex(random_bytes(32));
}
$csrf = $_SESSION['install_csrf'];
$detectedUrl = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://') . ($_SERVER['HTTP_HOST'] ?? 'localhost');
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installateur EstimIA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/install.css">
</head>
<body>
<div class="max-w-2xl mx-auto mt-10 mb-10 px-4">
    <div class="text-center mb-8">
        <h1 class="text-3xl font-extrabold bg-gradient-to-r from-blue-600 to-violet-600 bg-clip-text text-transparent">🏠 EstimIA</h1>
        <p class="text-gray-500 mt-1">Assistant d'installation</p>
    </div>

    <div class="bg-white border rounded-2xl shadow-sm p-6">
        <div class="mb-8">
            <div class="flex items-center gap-2" id="progressBar"></div>
            <div class="mt-2 grid grid-cols-5 text-[11px] text-gray-500 text-center">
                <span>Prérequis</span><span>Base de données</span><span>Configuration</span><span>Compte admin</span><span>Terminé</span>
            </div>
        </div>

        <div id="step1" class="step-content active">
            <h2 class="text-xl font-bold mb-2">1. Vérification des prérequis</h2>
            <div id="checksContainer" class="mb-4"></div>
            <p id="checksWarning" class="text-sm text-red-600 mb-4 hidden">Corrigez les erreurs avant de continuer.</p>
            <button id="step1Next" class="rounded-lg px-4 py-2 font-semibold bg-gray-200 text-gray-400" disabled>Continuer →</button>
        </div>

        <div id="step2" class="step-content">
            <h2 class="text-xl font-bold mb-2">2. Base de données</h2>
            <p class="text-sm text-gray-600 mb-4">Sur O2Switch, créez d'abord votre base et utilisateur dans cPanel &gt; Bases de données MySQL.</p>
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6 text-sm">
                <p class="font-semibold mb-2">ℹ️ Comment trouver ces informations ?</p>
                <ol class="list-decimal pl-5 space-y-1 text-amber-900">
                    <li>Connectez-vous à votre cPanel O2Switch</li>
                    <li>Allez dans « Bases de données MySQL »</li>
                    <li>Créez une base (ex: votre_prefixe_estimia)</li>
                    <li>Créez un utilisateur avec tous les privilèges</li>
                    <li>Associez l'utilisateur à la base</li>
                </ol>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold mb-1">Préfixe cPanel</label>
                    <input id="cpanel_prefix" type="text" class="w-full rounded-lg border px-3 py-2" placeholder="cpan1234">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1">Nom de la base (suffixe)</label>
                    <input id="db_suffix" type="text" class="w-full rounded-lg border px-3 py-2" placeholder="estimia">
                    <p class="text-xs text-gray-500 mt-1">Nom complet : <span id="db_preview">-</span></p>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1">Utilisateur DB (suffixe)</label>
                    <input id="db_user_suffix" type="text" class="w-full rounded-lg border px-3 py-2" placeholder="admin">
                    <p class="text-xs text-gray-500 mt-1">Utilisateur complet : <span id="db_user_preview">-</span></p>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1">Mot de passe DB</label>
                    <div class="flex gap-2">
                        <input id="db_pass" type="password" class="flex-1 rounded-lg border px-3 py-2">
                        <button type="button" id="toggleDbPass" class="rounded-lg border px-3">👁</button>
                        <button type="button" id="genDbPass" class="rounded-lg border px-3">Générer</button>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1">Host</label>
                    <input id="db_host" type="text" class="w-full rounded-lg border px-3 py-2 bg-gray-100" value="localhost" readonly>
                </div>
            </div>

            <div class="mt-5 flex items-center gap-2">
                <button id="testDb" class="rounded-lg border px-4 py-2 font-semibold">Tester la connexion</button>
                <span id="dbMessage" class="text-sm"></span>
            </div>

            <div class="mt-5 flex justify-between">
                <button class="rounded-lg border px-4 py-2" data-prev="1">← Retour</button>
                <button id="step2Next" class="rounded-lg px-4 py-2 font-semibold bg-gray-200 text-gray-400" disabled>Continuer →</button>
            </div>
        </div>

        <div id="step3" class="step-content">
            <h2 class="text-xl font-bold mb-4">3. Configuration du site</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold mb-1">Ville cible principale</label>
                    <input id="city_name" class="w-full rounded-lg border px-3 py-2" placeholder="Bordeaux">
                    <input type="hidden" id="city_lat" value="44.8378">
                    <input type="hidden" id="city_lng" value="-0.5792">
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-1">Rayon d'opération : <span id="radiusValue">30 km</span></label>
                    <input id="city_radius" type="range" min="10" max="50" step="5" value="30" class="w-full accent-blue-600">
                    <div id="mapPreview" class="mt-3 rounded-xl border overflow-hidden" style="height: 200px"></div>
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-1">Email principal</label>
                    <input id="notification_email" type="email" class="w-full rounded-lg border px-3 py-2" required>
                </div>

                <div>
                    <p class="text-sm font-semibold mb-1">Notifications</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-sm">
                        <label><input type="checkbox" id="notif_new_estimation" checked> Nouvelle estimation reçue</label>
                        <label><input type="checkbox" id="notif_new_rdv" checked> Nouveau RDV pris</label>
                        <label><input type="checkbox" id="notif_hot_lead" checked> Lead score &gt; 70</label>
                        <label><input type="checkbox" id="notif_weekly"> Rapport hebdomadaire</label>
                    </div>
                </div>

                <div class="border rounded-xl p-3">
                    <div class="flex gap-2 mb-3">
                        <button type="button" class="smtp-tab rounded-lg border px-3 py-1 bg-blue-600 text-white" data-mode="o2">SMTP O2Switch</button>
                        <button type="button" class="smtp-tab rounded-lg border px-3 py-1" data-mode="ext">SMTP Externe</button>
                    </div>
                    <div id="smtpO2">
                        <p class="text-xs text-gray-500 mb-2">Le SMTP host sera automatiquement mail.votredomaine.fr</p>
                        <input id="smtp_from" class="w-full rounded-lg border px-3 py-2 mb-2" placeholder="contact@mondomaine.fr">
                        <input id="smtp_pass" type="password" class="w-full rounded-lg border px-3 py-2" placeholder="Mot de passe email">
                        <input id="smtp_host" type="hidden">
                        <input id="smtp_port" type="hidden" value="465">
                        <input id="smtp_secure" type="hidden" value="ssl">
                        <input id="smtp_user" type="hidden">
                    </div>
                    <div id="smtpExt" class="hidden space-y-2">
                        <input id="smtp_host_ext" class="w-full rounded-lg border px-3 py-2" placeholder="smtp.gmail.com">
                        <input id="smtp_port_ext" type="number" class="w-full rounded-lg border px-3 py-2" value="587">
                        <select id="smtp_secure_ext" class="w-full rounded-lg border px-3 py-2"><option value="tls">TLS</option><option value="ssl">SSL</option><option value="">Aucune</option></select>
                        <input id="smtp_user_ext" class="w-full rounded-lg border px-3 py-2" placeholder="Utilisateur SMTP">
                        <input id="smtp_pass_ext" type="password" class="w-full rounded-lg border px-3 py-2" placeholder="Mot de passe SMTP">
                    </div>
                    <div class="mt-3 flex items-center gap-2">
                        <button type="button" id="testEmail" class="rounded-lg border px-4 py-2 text-sm">Envoyer un email test</button>
                        <span id="emailMessage" class="text-xs"></span>
                    </div>
                </div>

                <div class="grid sm:grid-cols-2 gap-3">
                    <div><label class="block text-sm font-semibold mb-1">Nom du site</label><input id="site_name" class="w-full rounded-lg border px-3 py-2" value="EstimIA"></div>
                    <div><label class="block text-sm font-semibold mb-1">Couleur principale</label><input id="site_color" type="color" class="w-full rounded-lg border px-3 py-2" value="#1a56db"></div>
                    <div><label class="block text-sm font-semibold mb-1">Téléphone</label><input id="phone" class="w-full rounded-lg border px-3 py-2"></div>
                    <div><label class="block text-sm font-semibold mb-1">URL du site</label><input id="site_url" class="w-full rounded-lg border px-3 py-2" value="<?php echo htmlspecialchars($detectedUrl, ENT_QUOTES, 'UTF-8'); ?>"></div>
                </div>
            </div>

            <div class="mt-5 flex justify-between">
                <button class="rounded-lg border px-4 py-2" data-prev="2">← Retour</button>
                <button class="rounded-lg bg-blue-600 text-white px-4 py-2 font-semibold" data-next="4">Continuer →</button>
            </div>
        </div>

        <div id="step4" class="step-content">
            <h2 class="text-xl font-bold mb-4">4. Compte administrateur</h2>
            <div class="space-y-3">
                <input id="admin_name" class="w-full rounded-lg border px-3 py-2" placeholder="Nom complet" required>
                <input id="admin_email" type="email" class="w-full rounded-lg border px-3 py-2" placeholder="Email admin" required>
                <div>
                    <input id="admin_password" type="password" class="w-full rounded-lg border px-3 py-2" placeholder="Mot de passe" required>
                    <div class="mt-1 h-2 bg-gray-200 rounded"><div id="pwdStrength" class="h-2 rounded bg-red-400" style="width:10%"></div></div>
                    <p id="pwdLabel" class="text-xs text-gray-500 mt-1">Faible</p>
                </div>
                <input id="admin_password_confirm" type="password" class="w-full rounded-lg border px-3 py-2" placeholder="Confirmer le mot de passe" required>
                <p id="pwdMatch" class="text-xs"></p>
            </div>

            <div class="mt-5 flex justify-between">
                <button class="rounded-lg border px-4 py-2" data-prev="3">← Retour</button>
                <button id="startInstall" class="rounded-lg bg-blue-600 text-white px-4 py-2 font-semibold">Installer EstimIA</button>
            </div>
        </div>

        <div id="step5" class="step-content">
            <h2 class="text-xl font-bold mb-4">5. Installation</h2>
            <div id="installTasks" class="space-y-2 mb-6"></div>
            <div id="successBox" class="hidden text-center p-6 rounded-xl border bg-green-50">
                <div class="text-5xl bounce-in">✅</div>
                <h3 class="text-2xl font-bold mt-3">EstimIA est installé ! 🎉</h3>
                <div id="summaryBox" class="mt-3 text-sm text-gray-600"></div>
                <div class="mt-4 flex flex-wrap justify-center gap-2">
                    <a href="../admin/" class="rounded-lg bg-blue-600 text-white px-4 py-2 font-semibold">Accéder au tableau de bord →</a>
                    <a href="../" class="rounded-lg border px-4 py-2 font-semibold">Voir mon site →</a>
                </div>
                <p class="text-xs text-amber-700 mt-3">Pour des raisons de sécurité, supprimez le dossier /install/ ou il sera automatiquement bloqué.</p>
            </div>
        </div>
    </div>
</div>

<script>
const csrfToken = <?php echo json_encode($csrf, JSON_UNESCAPED_UNICODE); ?>;
let currentStep = 1;
let checksAllOk = false;
let dbValidated = false;
let installMode = 'o2';
let installMap = null;
let installMarker = null;
let installCircle = null;
let autocomplete = null;

const progressNames = ['Prérequis','Base de données','Configuration','Compte admin','Terminé'];

function updateProgress(){
    const bar = document.getElementById('progressBar');
    bar.innerHTML = '';
    for(let i=1;i<=5;i++){
        const node = document.createElement('div');
        node.className = 'progress-node ' + (i < currentStep ? 'done' : (i === currentStep ? 'active' : 'future'));
        node.innerHTML = i < currentStep ? '✓' : i;
        bar.appendChild(node);
        if(i<5){
            const line = document.createElement('div');
            line.className = 'progress-line ' + (i < currentStep ? 'done' : '');
            bar.appendChild(line);
        }
    }
}

function showStep(step){
    currentStep = step;
    document.querySelectorAll('.step-content').forEach(el=>el.classList.remove('active'));
    document.getElementById('step'+step).classList.add('active');
    updateProgress();
}

function fullDbName(){
    const p = document.getElementById('cpanel_prefix').value.trim();
    const s = document.getElementById('db_suffix').value.trim();
    return p && s ? `${p}_${s}` : '';
}
function fullDbUser(){
    const p = document.getElementById('cpanel_prefix').value.trim();
    const s = document.getElementById('db_user_suffix').value.trim();
    return p && s ? `${p}_${s}` : '';
}

['cpanel_prefix','db_suffix','db_user_suffix'].forEach(id=>{
    document.addEventListener('input', (e)=>{
        if(['cpanel_prefix','db_suffix','db_user_suffix'].includes(e.target.id)){
            document.getElementById('db_preview').textContent = fullDbName() || '-';
            document.getElementById('db_user_preview').textContent = fullDbUser() || '-';
        }
    });
});

document.getElementById('toggleDbPass').addEventListener('click',()=>{
    const input = document.getElementById('db_pass');
    input.type = input.type === 'password' ? 'text' : 'password';
});

document.getElementById('genDbPass').addEventListener('click',()=>{
    const chars='ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789!@#$%&*';
    let pwd='';
    for(let i=0;i<18;i++) pwd += chars[Math.floor(Math.random()*chars.length)];
    document.getElementById('db_pass').value = pwd;
});

document.querySelectorAll('[data-prev]').forEach(btn=>btn.addEventListener('click',()=>showStep(Number(btn.dataset.prev))));
document.querySelectorAll('[data-next]').forEach(btn=>btn.addEventListener('click',()=>showStep(Number(btn.dataset.next))));

document.getElementById('step1Next').addEventListener('click',()=>showStep(2));
document.getElementById('step2Next').addEventListener('click',()=>showStep(3));

async function runChecks(){
    const res = await fetch('check.php');
    const data = await res.json();
    const map = {
        php_version:'Version PHP', pdo_mysql:'Extension pdo_mysql', json:'Extension json', mbstring:'Extension mbstring', curl:'Extension curl',
        config_writable:'Dossier config accessible', root_writable:'Racine accessible en écriture', session:'Sessions PHP', openssl:'Extension openssl'
    };
    const checks = document.getElementById('checksContainer');
    checks.innerHTML='';
    checksAllOk=true;

    Object.keys(map).forEach(key=>{
        const item = data[key];
        const ok = !!(item && item.ok);
        if(!ok) checksAllOk = false;
        const row = document.createElement('div');
        row.className = `check-item ${ok ? 'ok' : 'ko'} flex items-center gap-3 p-3 rounded-lg border mb-2`;
        let details='';
        if(key==='php_version') details = `(${item.current} / requis ${item.required})`;
        if(key==='config_writable') details = item.path || '';
        row.innerHTML = `<span>${ok?'✅':'❌'}</span><div><p class="font-semibold text-sm">${map[key]} <span class="text-xs text-gray-500">${details}</span></p>${ok?'':'<p class="text-xs text-red-600">Pré-requis manquant ou accès refusé.</p>'}</div>`;
        checks.appendChild(row);
    });

    const next = document.getElementById('step1Next');
    const warn = document.getElementById('checksWarning');
    if(checksAllOk){
        next.disabled = false; next.className='rounded-lg px-4 py-2 font-semibold bg-blue-600 text-white'; warn.classList.add('hidden');
    }else{
        next.disabled = true; next.className='rounded-lg px-4 py-2 font-semibold bg-gray-200 text-gray-400'; warn.classList.remove('hidden');
    }
}

async function testDb(){
    const params = new URLSearchParams();
    params.set('action','test_db');
    params.set('csrf_token', csrfToken);
    params.set('host',document.getElementById('db_host').value);
    params.set('db_name',fullDbName());
    params.set('db_user',fullDbUser());
    params.set('db_pass',document.getElementById('db_pass').value);

    const res = await fetch('process.php', {method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body: params});
    const data = await res.json();
    const box = document.getElementById('dbMessage');
    box.textContent = data.message || '';
    box.className = 'text-sm ' + (data.success ? 'text-green-600' : 'text-red-600');
    dbValidated = !!data.success;

    const next = document.getElementById('step2Next');
    if(dbValidated){ next.disabled=false; next.className='rounded-lg px-4 py-2 font-semibold bg-blue-600 text-white'; }
    else { next.disabled=true; next.className='rounded-lg px-4 py-2 font-semibold bg-gray-200 text-gray-400'; }
}

document.getElementById('testDb').addEventListener('click', testDb);

function currentSmtpValues(){
    if(installMode==='o2'){
        const from = document.getElementById('smtp_from').value.trim();
        const domain = from.includes('@') ? from.split('@')[1] : (window.location.host || 'localhost');
        return {
            smtp_host: `mail.${domain}`,
            smtp_port: '465',
            smtp_secure: 'ssl',
            smtp_user: from,
            smtp_pass: document.getElementById('smtp_pass').value,
            smtp_from: from,
        };
    }
    return {
        smtp_host: document.getElementById('smtp_host_ext').value.trim(),
        smtp_port: document.getElementById('smtp_port_ext').value.trim() || '587',
        smtp_secure: document.getElementById('smtp_secure_ext').value,
        smtp_user: document.getElementById('smtp_user_ext').value.trim(),
        smtp_pass: document.getElementById('smtp_pass_ext').value,
        smtp_from: document.getElementById('smtp_user_ext').value.trim(),
    };
}

document.querySelectorAll('.smtp-tab').forEach(tab=>tab.addEventListener('click',()=>{
    installMode = tab.dataset.mode;
    document.querySelectorAll('.smtp-tab').forEach(t=>t.className='smtp-tab rounded-lg border px-3 py-1');
    tab.className='smtp-tab rounded-lg border px-3 py-1 bg-blue-600 text-white';
    document.getElementById('smtpO2').classList.toggle('hidden', installMode!=='o2');
    document.getElementById('smtpExt').classList.toggle('hidden', installMode!=='ext');
}));

async function testEmail(){
    const params = new URLSearchParams();
    params.set('action','test_email');
    params.set('csrf_token', csrfToken);
    params.set('notification_email', document.getElementById('notification_email').value.trim());
    params.set('site_name', document.getElementById('site_name').value.trim());
    const res = await fetch('process.php',{method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:params});
    const data = await res.json();
    const box = document.getElementById('emailMessage');
    box.textContent = data.message || '';
    box.className = 'text-xs ' + (data.success ? 'text-green-600' : 'text-red-600');
}
document.getElementById('testEmail').addEventListener('click', testEmail);

const adminPassword = document.getElementById('admin_password');
const adminPasswordConfirm = document.getElementById('admin_password_confirm');

function updatePasswordUI(){
    const pwd = adminPassword.value;
    let score = 0;
    if(pwd.length >= 8) score += 1;
    if(/[A-Z]/.test(pwd)) score += 1;
    if(/[0-9]/.test(pwd)) score += 1;
    if(/[^A-Za-z0-9]/.test(pwd)) score += 1;
    const width = [10,30,55,80,100][score];
    const colors = ['bg-red-400','bg-red-400','bg-amber-400','bg-blue-500','bg-green-500'];
    const labels = ['Faible','Faible','Moyen','Bon','Fort'];
    const bar = document.getElementById('pwdStrength');
    bar.style.width = width+'%';
    bar.className = `h-2 rounded ${colors[score]}`;
    document.getElementById('pwdLabel').textContent = labels[score];

    const matchEl = document.getElementById('pwdMatch');
    if(adminPasswordConfirm.value==='') { matchEl.textContent=''; return; }
    if(pwd===adminPasswordConfirm.value){ matchEl.textContent='✓ Les mots de passe correspondent'; matchEl.className='text-xs text-green-600'; }
    else { matchEl.textContent='✗ Les mots de passe ne correspondent pas'; matchEl.className='text-xs text-red-600'; }
}
adminPassword.addEventListener('input', updatePasswordUI);
adminPasswordConfirm.addEventListener('input', updatePasswordUI);

document.getElementById('notification_email').addEventListener('input', e=>{ if(!document.getElementById('admin_email').value) document.getElementById('admin_email').value = e.target.value; });

document.getElementById('city_radius').addEventListener('input', (e)=>{
    document.getElementById('radiusValue').textContent = e.target.value + ' km';
    if(installCircle) installCircle.setRadius(Number(e.target.value) * 1000);
});

function initInstallMap(){
    const mapEl = document.getElementById('mapPreview');
    if(!mapEl || typeof google==='undefined' || !google.maps) return;

    const lat = Number(document.getElementById('city_lat').value || 44.8378);
    const lng = Number(document.getElementById('city_lng').value || -0.5792);
    installMap = new google.maps.Map(mapEl, {center:{lat,lng}, zoom:11});
    installMarker = new google.maps.Marker({position:{lat,lng}, map:installMap});
    installCircle = new google.maps.Circle({
        map: installMap,
        center: {lat,lng},
        radius: Number(document.getElementById('city_radius').value) * 1000,
        fillColor: '#1a56db', fillOpacity: 0.18, strokeColor: '#1a56db', strokeOpacity: 0.7, strokeWeight: 2,
    });

    const cityInput = document.getElementById('city_name');
    autocomplete = new google.maps.places.Autocomplete(cityInput, {
        types: ['(cities)'],
        componentRestrictions: {country: 'fr'},
        fields: ['geometry','name']
    });

    autocomplete.addListener('place_changed', ()=>{
        const place = autocomplete.getPlace();
        if(!place.geometry || !place.geometry.location) return;
        const latN = place.geometry.location.lat();
        const lngN = place.geometry.location.lng();
        document.getElementById('city_lat').value = latN;
        document.getElementById('city_lng').value = lngN;
        installMap.setCenter({lat:latN, lng:lngN});
        installMarker.setPosition({lat:latN, lng:lngN});
        installCircle.setCenter({lat:latN, lng:lngN});
    });
}
window.initInstallMap = initInstallMap;

function getPayloadForInstall(){
    const smtp = currentSmtpValues();
    return {
        host: document.getElementById('db_host').value,
        db_name: fullDbName(),
        db_user: fullDbUser(),
        db_pass: document.getElementById('db_pass').value,
        city_name: document.getElementById('city_name').value,
        city_lat: document.getElementById('city_lat').value,
        city_lng: document.getElementById('city_lng').value,
        city_radius: document.getElementById('city_radius').value,
        notification_email: document.getElementById('notification_email').value,
        notif_new_estimation: document.getElementById('notif_new_estimation').checked ? '1' : '',
        notif_new_rdv: document.getElementById('notif_new_rdv').checked ? '1' : '',
        notif_hot_lead: document.getElementById('notif_hot_lead').checked ? '1' : '',
        notif_weekly: document.getElementById('notif_weekly').checked ? '1' : '',
        site_name: document.getElementById('site_name').value,
        site_color: document.getElementById('site_color').value,
        phone: document.getElementById('phone').value,
        site_url: document.getElementById('site_url').value,
        admin_name: document.getElementById('admin_name').value,
        admin_email: document.getElementById('admin_email').value,
        admin_password: document.getElementById('admin_password').value,
        smtp_host: smtp.smtp_host,
        smtp_port: smtp.smtp_port,
        smtp_secure: smtp.smtp_secure,
        smtp_user: smtp.smtp_user,
        smtp_pass: smtp.smtp_pass,
        smtp_from: smtp.smtp_from,
    };
}

async function postInstallStep(step){
    const payload = getPayloadForInstall();
    const params = new URLSearchParams();
    params.set('action','install_step');
    params.set('csrf_token', csrfToken);
    params.set('step',step);
    Object.keys(payload).forEach(k=>params.set(k,payload[k] ?? ''));

    const res = await fetch('process.php', {method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body: params});
    return res.json();
}

async function startInstall(){
    if(adminPassword.value.length < 8){ alert('Mot de passe admin trop court (8 caractères minimum).'); return; }
    if(adminPassword.value !== adminPasswordConfirm.value){ alert('Les mots de passe ne correspondent pas.'); return; }

    showStep(5);

    const tasks = [
        ['connect','Connexion à la base de données...'],
        ['tables','Création des tables...'],
        ['seed','Insertion des données de référence...'],
        ['prices','Configuration des prix par ville...'],
        ['admin','Création du compte administrateur...'],
        ['config','Génération du fichier de configuration...'],
        ['email','Configuration des emails...'],
        ['secure','Sécurisation de l\'installation...'],
        ['finalize','Finalisation...'],
    ];

    const tasksBox = document.getElementById('installTasks');
    tasksBox.innerHTML = tasks.map(([key,label]) => `<div id="task-${key}" class="install-task p-3 rounded-lg border bg-white">⏳ ${label}</div>`).join('');

    let finalRedirect = '../admin/';
    for(const [key,label] of tasks){
        const row = document.getElementById('task-'+key);
        try {
            const data = await postInstallStep(key);
            if(!data.success){
                row.textContent = `❌ ${label} ${data.message || ''}`;
                row.classList.add('text-red-600');
                return;
            }
            row.textContent = `✅ ${label}`;
            row.classList.add('done');
            if(data.redirect) finalRedirect = data.redirect;
        } catch (e) {
            row.textContent = `❌ ${label} Erreur réseau`;
            row.classList.add('text-red-600');
            return;
        }
    }

    const summary = document.getElementById('summaryBox');
    summary.innerHTML = `
        <p>Site : <strong>${document.getElementById('site_url').value}</strong></p>
        <p>Ville cible : <strong>${document.getElementById('city_name').value}</strong> (rayon ${document.getElementById('city_radius').value} km)</p>
        <p>Admin : <strong>${document.getElementById('admin_email').value}</strong></p>
    `;

    document.getElementById('successBox').classList.remove('hidden');
}

document.getElementById('startInstall').addEventListener('click', startInstall);

runChecks();
updateProgress();
</script>
<script src="https://maps.googleapis.com/maps/api/js?libraries=places&callback=initInstallMap" async defer></script>
</body>
</html>
