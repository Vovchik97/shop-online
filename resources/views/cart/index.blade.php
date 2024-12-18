<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Корзина</title>
    <!-- Подключение Bootstrap CSS -->
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
                        @if (!request()->is('cart*'))
                            <li class="nav-item">
                                <a href="{{ route('cart.index') }}" class="btn btn-outline-primary me-2">
                                    🛒 Корзина
                                </a>
                            </li>
                        @endif
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


<!-- Приветственный блок корзины -->
<header class="bg-primary text-white text-center py-5">
    <div class="container">
        <h1 class="display-4">Корзина</h1>
        <p class="lead">Проверьте свои покупки и завершите оформление заказа</p>
    </div>
</header>

<!-- Контент страницы -->
<div class="container my-5">
    @if ($cartItems->isEmpty())
        <div class="alert alert-warning text-center">
            Ваша корзина пуста. <a href="{{ route('home') }}" class="btn btn-primary">Вернуться к покупкам</a>
        </div>
    @else
        <div class="row">
            @foreach ($cartItems as $item)
                <div class="col-lg-4 col-md-6 mb-4 d-flex align-items-stretch">
                    <div class="card w-100 d-flex flex-column">
                        <img src="{{ asset('storage/' . $item->product->image) }}" class="card-img-top" alt="{{ $item->product->name }}">
                        <div class="card-body d-flex flex-column">
                            <!-- Заголовок с фиксированной высотой -->
                            <h5 class="card-title text-truncate" style="max-width: 100%; min-height: 3rem;">
                                {{ $item->product->name }}
                            </h5>
                            <p class="card-text mb-3">
                                <strong>Цена:</strong> {{ $item->product->price }} ₽<br>
                                <strong>Количество:</strong> {{ $item->quantity }}<br>
                                <strong>Итого:</strong> {{ $item->product->price * $item->quantity }} ₽
                            </p>
                            <!-- Блок с кнопками выравнивается снизу -->
                            <div class="mt-auto">
                                <div class="d-flex justify-content-between align-items-center">
                                    <!-- Форма обновления -->
                                    <form action="{{ route('cart.store') }}" method="POST" class="d-flex align-items-center">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $item->product_id }}">
                                        <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" class="form-control me-2" style="width: 80px;">
                                        <button type="submit" class="btn btn-outline-primary btn-sm">Обновить</button>
                                    </form>

                                    <!-- Форма удаления -->
                                    <form action="{{ route('cart.destroy', $item->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm">Удалить</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Общая сумма -->
        <div class="text-end mt-4 p-3 bg-light border rounded">
            <h4>Общая сумма:
                <span class="text-success">{{ $cartItems->sum(fn($item) => $item->product->price * $item->quantity) }} ₽</span>
            </h4>
            <a href="#" onclick="event.preventDefault(); document.getElementById('order-form').submit();" class="btn btn-success">Перейти к оформлению</a>

            <form id="order-form" action="{{ route('order.store') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>
    @endif
</div>

<!-- Footer -->
<footer class="bg-light text-center py-3">
    <p>&copy; 2024 ВелоМото. Все права защищены.</p>
</footer>

<!-- Подключение Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
