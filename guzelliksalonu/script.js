// script.js — Sepet ve Randevu sistemi (geliştirilmiş)

const PRODUCTS = [
  { id: 1, title: "Cilt Temizleme Jeli", category: "Cilt Bakımı", price: 120, img: "https://images.unsplash.com/photo-1588774060219-7b1d9a8a0f7a?auto=format&fit=crop&w=600&q=60" },
  { id: 2, title: "Nemlendirici Krem", category: "Cilt Bakımı", price: 210, img: "https://images.unsplash.com/photo-1580201092322-5f52f8b8b9b7?auto=format&fit=crop&w=600&q=60" },
  { id: 3, title: "Ruj - Kırmızı", category: "Makyaj", price: 95, img: "https://images.unsplash.com/photo-1535523802789-0868a8a32a0d?auto=format&fit=crop&w=600&q=60" },
  { id: 4, title: "Saç Serumu", category: "Saç Ürünleri", price: 150, img: "https://images.unsplash.com/photo-1600180758890-1a4d3b8d1a2a?auto=format&fit=crop&w=600&q=60" },
];

const DB = {
  get: k => JSON.parse(localStorage.getItem(k) || "null"),
  set: (k, v) => localStorage.setItem(k, JSON.stringify(v)),
  remove: k => localStorage.removeItem(k)
};

// Kullanıcı
function registerUser(name,email,password){
  const users = DB.get("users") || [];
  if(users.find(u=>u.email===email)) throw new Error("Bu e-posta zaten kayıtlı.");
  users.push({name,email,password});
  DB.set("users", users);
  DB.set("currentUser", {name,email});
}
function loginUser(email,password){
  const users = DB.get("users") || [];
  const u = users.find(x=>x.email===email && x.password===password);
  if(!u) throw new Error("Bilgiler hatalı.");
  DB.set("currentUser", {name:u.name,email:u.email});
}
function logoutUser(){ DB.remove("currentUser"); location.reload(); }
function currentUser(){ return DB.get("currentUser"); }

// Sepet
function getCart(){ return DB.get("cart") || []; }
function setCart(c){ DB.set("cart", c); }
function addToCart(id){
  const cart = getCart();
  const p = PRODUCTS.find(x=>x.id===id);
  const item = cart.find(x=>x.id===id);
  if(item) item.qty++;
  else cart.push({...p, qty:1});
  setCart(cart);
  renderCartPanel();
}
function clearCart(){ DB.remove("cart"); renderCartPanel(); }

// Randevu sistemi
function getAppointments(){ return DB.get("appointments") || []; }
function setAppointments(a){ DB.set("appointments", a); }

function bookAppointment(service,date,time,duration,price){
  const user = currentUser();
  if(!user) throw new Error("Giriş yapmalısınız.");
  const list = getAppointments();

  // Aynı gün kontrolü
  const sameDay = list.find(a=>a.userEmail===user.email && a.date===date);
  if(sameDay) throw new Error("Aynı gün için sadece bir seans alabilirsiniz.");

  list.push({userEmail:user.email,service,date,time,duration,price});
  setAppointments(list);
  alert(`🎉 ${date} tarihli ${service} randevunuz başarıyla oluşturuldu!`);
  renderAppointments();
}

// UI
function renderUserPanel(){
  const el=document.getElementById("userPanel");
  const u=currentUser();
  el.innerHTML=u?`Hoşgeldin, ${u.name} <button class="btn small" onclick="logoutUser()">Çıkış</button>`:`<a class="btn small" href="login.html">Giriş / Kayıt</a>`;
}

// Sepet sadece ürün sayfasında
function renderCartPanel(){
  const isProductPage = location.pathname.includes("hizmetler.html");
  let panel = document.getElementById("cartPanelRoot");
  if(panel) panel.remove();
  if(!isProductPage) return;
  panel = document.createElement("div");
  panel.id="cartPanelRoot";
  panel.className="cart-panel";
  document.body.appendChild(panel);

  const cart = getCart();
  const total = cart.reduce((t,i)=>t+i.price*i.qty,0);
  panel.innerHTML=`
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px">
      <strong>Sepet (${cart.length})</strong>
      <button class="btn small" id="clearBtn">Temizle</button>
    </div>
    ${cart.map(i=>`<div style="margin-bottom:6px">${i.title} (${i.qty}x) — ${i.price*i.qty}₺</div>`).join("") || "<div>Sepet boş.</div>"}
    <div style="margin-top:10px;text-align:right"><strong>Toplam: ${total}₺</strong></div>
  `;
  document.getElementById("clearBtn").onclick=()=>{ if(confirm("Temizlensin mi?")) clearCart(); };
}

// Ürün listesi
function renderProducts(){
  const list=document.getElementById("productList");
  if(!list) return;
  list.innerHTML=PRODUCTS.map(p=>`
  <div class="card product">
    <img src="${p.img}" alt="${p.title}">
    <div class="product-info">
      <strong>${p.title}</strong><br>
      <span>${p.price}₺</span><br>
      <button class="btn small" onclick="addToCart(${p.id})">Sepete Ekle</button>
    </div>
  </div>`).join("");
}

// Randevu
function renderAppointments(){
  const container=document.getElementById("myAppointments");
  if(!container) return;
  const user=currentUser();
  if(!user){ container.innerHTML="<div>Giriş yapmalısınız.</div>"; return; }
  const apps=getAppointments().filter(a=>a.userEmail===user.email);
  container.innerHTML=apps.map(a=>`
    <div class="card">
      <b>${a.service}</b> — ${a.date} ${a.time} (${a.duration} dk)<br>
      <small>Ücret: ${a.price}₺</small>
    </div>
  `).join("") || "<div>Henüz randevunuz yok.</div>";
}

// Randevu formu
function initRandevuForm(){
  const form=document.getElementById("randevuForm");
  if(!form) return;

  const timeSelect=form.querySelector("[name=time]");
  const durationSelect=form.querySelector("[name=duration]");
  const dateInput=form.querySelector("[name=date]");

  // Saat aralıklarını üret
  const hours=[];
  for(let h=9;h<=18;h++){ hours.push(`${String(h).padStart(2,"0")}:00`); }
  timeSelect.innerHTML=hours.map(h=>`<option value="${h}">${h}</option>`).join("");

  // Süre aralıkları
  durationSelect.innerHTML=["15","30","45","60"].map(d=>`<option value="${d}">${d} dk</option>`).join("");

  // Tarih/saat değiştikçe dolu seansları soluklaştır
  dateInput.addEventListener("change",disableTaken);
  timeSelect.addEventListener("click",disableTaken);

  function disableTaken(){
    const date=dateInput.value;
    const apps=getAppointments().filter(a=>a.date===date);
    Array.from(timeSelect.options).forEach(opt=>{
      if(apps.find(a=>a.time===opt.value)) opt.disabled=true, opt.classList.add("unavailable");
      else opt.disabled=false, opt.classList.remove("unavailable");
    });
  }

  form.addEventListener("submit",e=>{
    e.preventDefault();
    const service=form.service.value;
    const date=form.date.value;
    const time=form.time.value;
    const duration=form.duration.value;
    const price=form.price.value;
    try{
      bookAppointment(service,date,time,duration,price);
      form.reset();
      renderAppointments();
    }catch(err){ alert(err.message); }
  });

  renderAppointments();
}

// Login/Register
function initAuth(){
  const reg=document.getElementById("registerForm");
  const log=document.getElementById("loginForm");
  if(reg){
    reg.onsubmit=e=>{
      e.preventDefault();
      try{
        registerUser(reg.name.value,reg.email.value,reg.password.value);
        alert("Kayıt başarılı!");
        location.href="index.html";
      }catch(err){ alert(err.message); }
    };
  }
  if(log){
    log.onsubmit=e=>{
      e.preventDefault();
      try{
        loginUser(log.email.value,log.password.value);
        alert("Giriş başarılı.");
        location.href="index.html";
      }catch(err){ alert(err.message); }
    };
  }
}

// INIT
document.addEventListener("DOMContentLoaded",()=>{
  renderUserPanel();
  renderCartPanel();
  renderProducts();
  initAuth();
  initRandevuForm();
});
