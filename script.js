const booksDB = [
  { id: 1, title: "Crime e Castigo", author: "Fiódor Dostoiévski", category: "Literatura clássica russa", status: "quero_ler" },
  { id: 2, title: "Noites Brancas", author: "Fiódor Dostoiévski", category: "Literatura clássica russa", status: "quero_ler" },
  { id: 3, title: "Os Irmãos Karamázov", author: "Fiódor Dostoiévski", category: "Literatura clássica russa", status: "quero_ler" },
  { id: 4, title: "O Idiota", author: "Fiódor Dostoiévski", category: "Literatura clássica russa", status: "quero_ler" },
  { id: 5, title: "Ressurreição", author: "Liev Tolstói", category: "Literatura clássica russa", status: "quero_ler" },
  { id: 6, title: "A Morte de Ivan Ilitch", author: "Liev Tolstói", category: "Literatura clássica russa", status: "quero_ler" },
  { id: 7, title: "Almas Mortas", author: "Nikolai Gógol", category: "Literatura clássica russa", status: "quero_ler" },
  { id: 8, title: "O Príncipe", author: "Nicolau Maquiavel", category: "Estratégia e política", status: "quero_ler" },
  { id: 9, title: "A Arte da Guerra", author: "Sun Tzu", category: "Estratégia e política", status: "quero_ler" },
  { id: 10, title: "As 48 Leis do Poder", author: "Robert Greene", category: "Estratégia e política", status: "quero_ler" },
  { id: 11, title: "Manual de Persuasão do FBI", author: "Jack Schafer", category: "Psicologia, persuasão e comportamento", status: "quero_ler" },
  { id: 12, title: "O que Todo Corpo Fala", author: "Joe Navarro", category: "Psicologia, persuasão e comportamento", status: "quero_ler" },
  { id: 13, title: "1984", author: "George Orwell", category: "Distopias e crítica política", status: "quero_ler" },
  { id: 14, title: "A Revolução dos Bichos", author: "George Orwell", category: "Distopias e crítica política", status: "quero_ler" },
  { id: 15, title: "O Quinto Mandamento", author: "Autor a confirmar", category: "Outros", status: "quero_ler" },
  { id: 16, title: "O Pequeno Príncipe", author: "Antoine de Saint-Exupéry", category: "Outros", status: "quero_ler" }
];

const userProfile = {
  name: "Yuri Marinho",
  bio: "Leitor de clássicos russos, estratégia, psicologia e distopias.",
  favoriteCategories: [
    "Literatura clássica russa",
    "Estratégia e política",
    "Psicologia, persuasão e comportamento",
    "Distopias e crítica política"
  ]
};

let books = JSON.parse(localStorage.getItem("yuriBooks")) || booksDB;

const booksContainer = document.getElementById("booksContainer");
const searchInput = document.getElementById("searchInput");
const categoryFilter = document.getElementById("categoryFilter");
const statusFilter = document.getElementById("statusFilter");

function saveBooks() {
  localStorage.setItem("yuriBooks", JSON.stringify(books));
}

function getStatusLabel(status) {
  const labels = {
    lido: "✅ Lido",
    lendo: "📖 Lendo",
    quero_ler: "⭐ Quero ler"
  };

  return labels[status];
}

function renderCategories() {
  const categories = [...new Set(books.map(book => book.category))];

  categories.forEach(category => {
    const option = document.createElement("option");
    option.value = category;
    option.textContent = category;
    categoryFilter.appendChild(option);
  });
}

function renderBooks() {
  const search = searchInput.value.toLowerCase();
  const selectedCategory = categoryFilter.value;
  const selectedStatus = statusFilter.value;

  const filteredBooks = books.filter(book => {
    const matchesSearch =
      book.title.toLowerCase().includes(search) ||
      book.category.toLowerCase().includes(search) ||
      book.author.toLowerCase().includes(search);

    const matchesCategory =
      selectedCategory === "todos" || book.category === selectedCategory;

    const matchesStatus =
      selectedStatus === "todos" || book.status === selectedStatus;

    return matchesSearch && matchesCategory && matchesStatus;
  });

  booksContainer.innerHTML = "";

  if (filteredBooks.length === 0) {
    booksContainer.innerHTML = `<p class="empty">Nenhum livro encontrado.</p>`;
    return;
  }

  filteredBooks.forEach(book => {
    const card = document.createElement("article");
    card.className = "book-card";

    card.innerHTML = `
      <div class="book-cover">📘</div>
      <h2>${book.title}</h2>
      <p class="author">${book.author}</p>
      <span class="category">${book.category}</span>
      <p class="status ${book.status}">${getStatusLabel(book.status)}</p>

      <div class="buttons">
        <button class="btn-read" onclick="changeStatus(${book.id}, 'lido')">Já li</button>
        <button class="btn-reading" onclick="changeStatus(${book.id}, 'lendo')">Estou lendo</button>
        <button class="btn-want" onclick="changeStatus(${book.id}, 'quero_ler')">Quero ler</button>
      </div>
    `;

    booksContainer.appendChild(card);
  });

  updateStats();
}

function changeStatus(bookId, newStatus) {
  books = books.map(book => {
    if (book.id === bookId) {
      return { ...book, status: newStatus };
    }

    return book;
  });

  saveBooks();
  renderBooks();
}

function updateStats() {
  document.getElementById("totalBooks").textContent = books.length;
  document.getElementById("readBooks").textContent = books.filter(book => book.status === "lido").length;
  document.getElementById("readingBooks").textContent = books.filter(book => book.status === "lendo").length;
  document.getElementById("wantBooks").textContent = books.filter(book => book.status === "quero_ler").length;
}

// Funções futuras para transformar em rede social

function addFriend(friendProfile) {
  // Futuramente: adicionar amigo à lista do Yuri.
}

function compareBooksWithFriend(friendBooks) {
  // Futuramente: comparar livros em comum entre Yuri e um amigo.
}

function calculateLiteraryCompatibility(friendBooks) {
  // Futuramente: calcular porcentagem de compatibilidade literária.
}

function createUserProfile(profileData) {
  // Futuramente: editar ou criar perfil do usuário.
}

function addNewBook(bookData) {
  // Futuramente: permitir que Yuri adicione novos livros manualmente.
}

searchInput.addEventListener("input", renderBooks);
categoryFilter.addEventListener("change", renderBooks);
statusFilter.addEventListener("change", renderBooks);

renderCategories();
renderBooks();
updateStats();
