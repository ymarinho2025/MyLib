const API = "";

const $ = (id) => document.getElementById(id);

function token() {
  return localStorage.getItem("token") || "";
}

function currentUser() {
  return JSON.parse(localStorage.getItem("user") || "null");
}

async function api(path, opts = {}) {
  opts.headers = {
    ...(opts.headers || {}),
    "Content-Type": "application/json"
  };

  if (token()) {
    opts.headers.Authorization = "Bearer " + token();
  }

  const r = await fetch(API + path, opts);
  const j = await r.json().catch(() => ({}));

  if (!r.ok) throw j;

  return j;
}

function renderNav() {
  const nav = $("nav");
  const user = currentUser();

  if (!nav) return;

  if (token() && user) {
    nav.innerHTML = `
      <a href="/home.php">Home</a>
      <a href="/explorar.php">Explorar</a>
      <a href="/books.php">Livros</a>
      <a href="/profile.php?username=${user.username}">Meu Perfil</a>
      <button onclick="logout()">Sair</button>
    `;
  } else {
    nav.innerHTML = `
      <a href="/login-page.php">Login</a>
      <a href="/cadastro.php">Cadastro</a>
      <a href="/explorar.php">Explorar</a>
    `;
  }
}

function logout() {
  localStorage.removeItem("token");
  localStorage.removeItem("user");
  window.location.href = "/login-page.php";
}

async function login() {
  try {
    const j = await api("/login.php", {
      method: "POST",
      body: JSON.stringify({
        login: $("login").value,
        password: $("password").value
      })
    });

    localStorage.setItem("token", j.token);
    localStorage.setItem("user", JSON.stringify(j.user));

    if ($("out")) $("out").textContent = "Logado como @" + j.user.username;

    window.location.href = "/home.php";
  } catch (e) {
    if ($("out")) $("out").textContent = JSON.stringify(e, null, 2);
  }
}

async function register() {
  try {
    const j = await api("/register.php", {
      method: "POST",
      body: JSON.stringify({
        name: $("name").value,
        username: $("username").value,
        email: $("email").value,
        password: $("password").value,
        bio: $("bio").value
      })
    });

    localStorage.setItem("token", j.token);
    localStorage.setItem("user", JSON.stringify(j.user));

    if ($("out")) $("out").textContent = "Cadastro realizado: @" + j.user.username;

    window.location.href = "/home.php";
  } catch (e) {
    if ($("out")) $("out").textContent = JSON.stringify(e, null, 2);
  }
}

async function loadBooks() {
  const el = $("books");
  if (!el) return;

  try {
    const j = await api("/books.php");

    el.innerHTML = j.books.map(b => `
      <div class="card">
        <b>${b.title}</b><br>
        ${b.author}<br>
        <span class="badge">${b.genre}</span><br><br>

        <button onclick="addBook(${b.id}, 'READING')">Lendo</button>
        <button onclick="addBook(${b.id}, 'READ')">Lido</button>
        <button onclick="addBook(${b.id}, 'NEXT_READ')">Próxima leitura</button>
        <button onclick="addBook(${b.id}, 'WANT_TO_OWN')">Desejo ter</button>
        <button onclick="addBook(${b.id}, 'DUSTY')">Pegando poeira</button>
        <button onclick="addBook(${b.id}, 'GIFT_ACCEPTED')">Aceito presente</button>
      </div>
    `).join("");
  } catch (e) {
    el.innerHTML = `<pre>${JSON.stringify(e, null, 2)}</pre>`;
  }
}

async function addBook(book_id, status) {
  try {
    const j = await api("/add-book.php", {
      method: "POST",
      body: JSON.stringify({ book_id, status })
    });

    alert("Livro adicionado/atualizado!");
  } catch (e) {
    alert(JSON.stringify(e, null, 2));
  }
}

async function searchUsers() {
  const q = $("searchUsers")?.value || "";
  const el = $("users");

  if (!el) return;

  try {
    const j = await api("/users.php?q=" + encodeURIComponent(q));

    el.innerHTML = j.users.map(u => `
      <div class="card">
        <b>${u.name}</b> @${u.username}
        <p>${u.bio || ""}</p>
        <span class="badge">${u.total_books || 0} livros</span>
        <br><br>
        <a href="/profile.php?username=${u.username}">
          <button>Ver perfil</button>
        </a>
        ${token() ? `<button onclick="followUser('${u.username}')">Seguir</button>` : ""}
      </div>
    `).join("");
  } catch (e) {
    el.innerHTML = `<pre>${JSON.stringify(e, null, 2)}</pre>`;
  }
}

async function followUser(username) {
  try {
    await api("/follow.php", {
      method: "POST",
      body: JSON.stringify({ username })
    });

    alert("Usuário seguido!");
  } catch (e) {
    alert(JSON.stringify(e, null, 2));
  }
}

async function loadProfile() {
  const el = $("profile");
  if (!el) return;

  const params = new URLSearchParams(window.location.search);
  const logged = currentUser();
  const username = params.get("username") || logged?.username;

  if (!username) {
    window.location.href = "/login-page.php";
    return;
  }

  try {
    const j = await api("/profile.php?username=" + encodeURIComponent(username));
    const u = j.user;
    const books = j.books || [];

    el.innerHTML = `
      <div class="card">
        <h2>${u.name}</h2>
        <p>@${u.username}</p>
        <p>${u.bio || ""}</p>
        <span class="badge">${books.length} livros</span>
        ${logged && logged.username !== u.username ? `
          <button onclick="followUser('${u.username}')">Seguir</button>
        ` : ""}
      </div>

      <h2>Livros do perfil</h2>

      ${books.length === 0 ? `<p>Nenhum livro adicionado ainda.</p>` : books.map(b => `
        <div class="card">
          <b>${b.title}</b><br>
          ${b.author}<br>
          <span class="badge">${b.genre}</span>
          <span class="badge">${translateStatus(b.status)}</span>
          ${b.rating ? `<p>Nota: ${b.rating}/5</p>` : ""}
          ${b.notes ? `<p>${b.notes}</p>` : ""}

          ${logged && logged.username === u.username ? `
            <br>
            <textarea id="note-${b.book_id}" placeholder="Comentário pessoal">${b.notes || ""}</textarea>
            <input id="rating-${b.book_id}" type="number" min="1" max="5" value="${b.rating || ""}" placeholder="Nota">
            <button onclick="updateMyBook(${b.book_id})">Salvar comentário</button>
          ` : ""}
        </div>
      `).join("")}
    `;
  } catch (e) {
    el.innerHTML = `<pre>${JSON.stringify(e, null, 2)}</pre>`;
  }
}

function translateStatus(status) {
  const map = {
    READ: "Lido",
    READING: "Lendo",
    NEXT_READ: "Próxima leitura",
    WANT_TO_OWN: "Desejo ter futuramente",
    DUSTY: "Pegando poeira",
    GIFT_ACCEPTED: "Aceito presente"
  };

  return map[status] || status;
}

async function updateMyBook(book_id) {
  try {
    await api("/update-book.php", {
      method: "POST",
      body: JSON.stringify({
        book_id,
        notes: $("note-" + book_id).value,
        rating: $("rating-" + book_id).value
      })
    });

    alert("Livro atualizado!");
    loadProfile();
  } catch (e) {
    alert(JSON.stringify(e, null, 2));
  }
}

$("searchUsers")?.addEventListener("input", searchUsers);

renderNav();
loadBooks();
searchUsers();
loadProfile();