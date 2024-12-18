<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Интернет-магазин</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<!-- Навигационная панель -->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('home') }}">ВелоМото</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link active" href="{{ route('home') }}">Главная</a>
                </li>
            </ul>
            <form class="d-flex flex-grow-1 me-3" method="GET" action="{{ route('home') }}">
                <input class="form-control me-2" type="search" name="search" placeholder="Поиск товаров" value="{{ request('search') }}">
                <button class="btn btn-outline-success" type="submit">Поиск</button>
            </form>
            <ul class="navbar-nav">
                @auth
                    @if(auth()->check() && auth()->user()->role === 'admin')
                        <li><a href="{{ url('/admin-panel') }}" class="btn btn-outline-primary me-2">Админ-панель</a></li>
                    @endif
                    <li class="nav-item">
                        <a href="{{ route('cart.index') }}" class="btn btn-outline-primary me-2">
                            <i class="bi bi-cart"></i> 🛒 Корзина
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('orders.history') }}" class="btn btn-outline-primary me-2">
                            <i class="bi bi-cart"></i> История заказов
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-outline-danger" href="{{ route('logout') }}"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            Выйти
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="btn btn-outline-primary me-2" href="{{ route('login') }}">Вход</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-outline-secondary" href="{{ route('register') }}">Регистрация</a>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>


<!-- Приветственный блок -->
<header class="bg-primary text-white text-center py-5">
    <div class="container">
        <h1 class="display-4">Добро пожаловать в интернет-магазин ВелоМото!</h1>
        <p class="lead">Лучший выбор велосипедов и мотоциклов в одном месте</p>
    </div>
</header>

<!-- Контент -->
<div class="container my-5">
    <div class="row">
        <!-- Секция категорий -->
        <div class="col-md-3">
            <h4>Фильтры</h4>
            <form method="GET" action="{{ route('home') }}">
                <!-- Категории -->
                <div class="mb-3">
                    <label for="category" class="form-label">Категория</label>
                    <select class="form-select" id="category" name="category">
                        <option value="">Все категории</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Фильтр по цене -->
                <div class="mb-3">
                    <label for="price_from" class="form-label">Цена от</label>
                    <input type="number" class="form-control" id="price_from" name="price_from" value="{{ request('price_from') }}" placeholder="Минимальная цена">
                </div>
                <div class="mb-3">
                    <label for="price_to" class="form-label">Цена до</label>
                    <input type="number" class="form-control" id="price_to" name="price_to" value="{{ request('price_to') }}" placeholder="Максимальная цена">
                </div>

                <button type="submit" class="btn btn-primary w-100">Применить фильтры</button>
            </form>
        </div>
        <!-- Секция товаров -->
        <div class="col-md-9">
            <h2 class="text-center">Популярные товары</h2>
            <div class="row row-cols-1 row-cols-md-3 g-4 mt-4">
                @forelse ($products as $product)
                    <div class="col d-flex">
                        <div class="card w-100">
                            <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top" alt="{{ $product->name }}" style="height: 200px; object-fit: cover;">
                            <div class="card-body d-flex flex-column">
                                <!-- Ограничение названия на одной строке -->
                                <h5 class="card-title text-truncate" style="max-width: 100%;">{{ $product->name }}</h5>
                                <p class="card-text">{{ $product->price }} ₽</p>
                                <div class="mt-auto">
                                    <a href="{{ route('product.show', $product->id) }}" class="btn btn-primary w-100 mb-2">Подробнее</a>

                                    @auth
                                        <form action="{{ route('cart.store') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                                            <input type="hidden" name="quantity" value="1">
                                            <button type="submit" class="btn btn-primary w-100 mb-2">Добавить в корзину</button>
                                        </form>
                                    @else
                                        <a href="{{ route('login') }}" class="btn btn-secondary w-100 mb-2">Войдите, чтобы добавить в корзину</a>
                                    @endauth
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-center">Нет доступных товаров.</p>
                @endforelse
            </div>
            <div class="mt-3">
                {{ $products->links() }}
            </div>

        </div>
    </div>
</div>

<!-- Footer -->
<footer class="bg-light text-center py-3">
    <p>&copy; 2024 ВелоМото. Все права защищены.</p>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                icon: 'success',
                title: 'Успех!',
                text: '{{ session('success') }}',
                confirmButtonText: 'Ок',
            });
        });
    </script>
@endif
</body>
</html>
