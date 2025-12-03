// Kullanıcı panelini güncelle
function kullaniciPaneliniGuncelle() {
  const panel = document.getElementById('userPanel');
  const aktif = JSON.parse(localStorage.getItem('aktifKullanici'));
  if (aktif) {
    panel.innerHTML = `
      <div style="display:inline-block; position:relative;">
        <button onclick="toggleMenu()" style="background:#e91e63; color:white; border:none; padding:8px; border-radius:5px; cursor:pointer;">
          Kullanıcı: ${aktif.adSoyad}
        </button>
        <div id="userMenu" style="display:none; position:absolute; background:white; border:1px solid #ccc; right:0; top:40px; z-index:999;">
          <a href="profil.html" style="display:block; padding:10px;">Profil</a>
          <a href="siparislerim.html" style="display:block; padding:10px;">Siparişlerim</a>
          <a href="#" onclick="cikisYap()" style="display:block; padding:10px;">Çıkış Yap</a>
        </div>
      </div>
    `;
  } else {
    panel.innerHTML = `<a href="kullanicigirisi.html">Giriş / Kayıt</a>`;
  }
}

// Menü aç/kapa
function toggleMenu() {
  const menu = document.getElementById('userMenu');
  if (menu) {
    menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
  }
}

// Çıkış yap
function cikisYap() {
  localStorage.removeItem('aktifKullanici');
  alert("Çıkış yapıldı.");
  window.location.href = 'index.html';
}

// Sayfa yüklendiğinde kullanıcı panelini güncelle
document.addEventListener("DOMContentLoaded", kullaniciPaneliniGuncelle);
