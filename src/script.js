// Vite & Gourmand JavaScript Application

class ViteGourmandApp {
    constructor() {
        this.cart = [];
        this.menus = [];
        this.eventTypes = [];
        this.currentFilter = 'all';
        
        this.init();
    }

    async init() {
        this.bindEvents();
        await this.loadData();
        this.updateCartDisplay();
    }

    bindEvents() {
        // Navigation
        document.querySelectorAll('.nav-btn').forEach(btn => {
            btn.addEventListener('click', (e) => this.switchSection(e.target.dataset.section));
        });

        // Filter buttons
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('filter-btn')) {
                this.filterMenus(e.target.dataset.filter);
            }
        });

        // Modal events
        document.getElementById('menu-modal').addEventListener('click', (e) => {
            if (e.target === document.getElementById('menu-modal') || e.target.classList.contains('close')) {
                this.closeModal('menu-modal');
            }
        });

        document.getElementById('success-modal').addEventListener('click', (e) => {
            if (e.target === document.getElementById('success-modal') || e.target.id === 'close-success') {
                this.closeModal('success-modal');
            }
        });

        // Cart events
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('add-to-cart')) {
                const menuId = e.target.dataset.menuId;
                this.addToCart(menuId);
            }
            
            if (e.target.classList.contains('view-menu')) {
                const menuId = e.target.dataset.menuId;
                this.showMenuDetail(menuId);
            }

            if (e.target.classList.contains('quantity-btn')) {
                const action = e.target.dataset.action;
                const menuId = e.target.dataset.menuId;
                this.updateQuantity(menuId, action);
            }
        });

        // Order form submission
        document.addEventListener('submit', (e) => {
            if (e.target.id === 'order-form') {
                e.preventDefault();
                this.submitOrder();
            }
        });
    }

    switchSection(section) {
        // Update navigation
        document.querySelectorAll('.nav-btn').forEach(btn => btn.classList.remove('active'));
        document.querySelector(`[data-section="${section}"]`).classList.add('active');

        // Update sections
        document.querySelectorAll('.section').forEach(sec => sec.classList.remove('active'));
        document.getElementById(`${section}-section`).classList.add('active');

        // Update cart content when switching to cart
        if (section === 'cart') {
            this.updateCartSection();
        }
    }

    async loadData() {
        this.showLoading(true);
        
        try {
            // Load menus
            const menusResponse = await fetch('/api/menus.php');
            const menusData = await menusResponse.json();
            
            if (menusData.success) {
                this.menus = menusData.data;
                this.extractEventTypes();
                this.renderEventFilters();
                this.renderMenus();
            }
        } catch (error) {
            console.error('Error loading data:', error);
            this.showError('Erreur lors du chargement des données');
        } finally {
            this.showLoading(false);
        }
    }

    extractEventTypes() {
        const types = [...new Set(this.menus.map(menu => menu.event_type))];
        this.eventTypes = types.sort();
    }

    renderEventFilters() {
        const filtersContainer = document.getElementById('event-filters');
        filtersContainer.innerHTML = '';
        
        this.eventTypes.forEach(type => {
            const button = document.createElement('button');
            button.className = 'filter-btn';
            button.dataset.filter = type;
            button.innerHTML = `<i class="fas fa-tag"></i> ${type}`;
            filtersContainer.appendChild(button);
        });
    }

    filterMenus(filter) {
        // Update active filter button
        document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
        document.querySelector(`[data-filter="${filter}"]`).classList.add('active');
        
        this.currentFilter = filter;
        this.renderMenus();
    }

    renderMenus() {
        const grid = document.getElementById('menus-grid');
        const filteredMenus = this.currentFilter === 'all' 
            ? this.menus 
            : this.menus.filter(menu => menu.event_type === this.currentFilter);

        grid.innerHTML = '';

        if (filteredMenus.length === 0) {
            grid.innerHTML = '<div class="no-menus">Aucun menu disponible pour ce type d\'événement.</div>';
            return;
        }

        filteredMenus.forEach(menu => {
            const menuCard = this.createMenuCard(menu);
            grid.appendChild(menuCard);
        });
    }

    createMenuCard(menu) {
        const card = document.createElement('div');
        card.className = 'menu-card';
        
        // Limit items display to first 3
        const displayItems = menu.items.slice(0, 3);
        const hasMoreItems = menu.items.length > 3;
        
        card.innerHTML = `
            <div class="menu-header">
                <h3>${menu.name}</h3>
                <div class="event-type">${menu.event_type}</div>
                <div class="price">${menu.price.toFixed(2)}€ <span class="price-unit">${menu.serves}</span></div>
            </div>
            <div class="menu-content">
                <p class="menu-description">${menu.description}</p>
                <div class="menu-items">
                    ${displayItems.map(item => `
                        <div class="menu-item">
                            <div class="item-info">
                                <div class="item-category">${item.category}</div>
                                <h4>${item.name}</h4>
                                <p class="item-description">${item.description}</p>
                            </div>
                        </div>
                    `).join('')}
                    ${hasMoreItems ? `<div class="more-items">Et ${menu.items.length - 3} autre${menu.items.length - 3 > 1 ? 's' : ''} spécialité${menu.items.length - 3 > 1 ? 's' : ''}...</div>` : ''}
                </div>
                <div class="menu-actions">
                    <button class="btn btn-primary add-to-cart" data-menu-id="${menu._id}">
                        <i class="fas fa-plus"></i> Ajouter au panier
                    </button>
                    <button class="btn btn-outline view-menu" data-menu-id="${menu._id}">
                        <i class="fas fa-eye"></i> Voir détails
                    </button>
                </div>
            </div>
        `;
        
        return card;
    }

    showMenuDetail(menuId) {
        const menu = this.menus.find(m => m._id === menuId);
        if (!menu) return;

        const modal = document.getElementById('menu-modal');
        const detail = document.getElementById('menu-detail');
        
        detail.innerHTML = `
            <div class="menu-detail-header">
                <h2>${menu.name}</h2>
                <div class="event-type">${menu.event_type}</div>
                <div class="price">${menu.price.toFixed(2)}€ <span class="price-unit">${menu.serves}</span></div>
            </div>
            <p class="menu-description">${menu.description}</p>
            <div class="menu-items-detailed">
                <h3>Composition du menu :</h3>
                ${menu.items.map(item => `
                    <div class="menu-item-detailed">
                        <div class="item-category">${item.category}</div>
                        <h4>${item.name}</h4>
                        <p class="item-description">${item.description}</p>
                    </div>
                `).join('')}
            </div>
            <div class="menu-actions">
                <button class="btn btn-primary add-to-cart" data-menu-id="${menu._id}">
                    <i class="fas fa-plus"></i> Ajouter au panier
                </button>
            </div>
        `;
        
        modal.classList.add('show');
    }

    addToCart(menuId) {
        const menu = this.menus.find(m => m._id === menuId);
        if (!menu) return;

        const existingItem = this.cart.find(item => item.menu._id === menuId);
        
        if (existingItem) {
            existingItem.quantity++;
        } else {
            this.cart.push({
                menu: menu,
                quantity: 1
            });
        }
        
        this.updateCartDisplay();
        this.showNotification('Menu ajouté au panier !');
        this.closeModal('menu-modal');
    }

    updateQuantity(menuId, action) {
        const cartItem = this.cart.find(item => item.menu._id === menuId);
        if (!cartItem) return;

        if (action === 'increase') {
            cartItem.quantity++;
        } else if (action === 'decrease') {
            cartItem.quantity--;
            if (cartItem.quantity <= 0) {
                this.cart = this.cart.filter(item => item.menu._id !== menuId);
            }
        }

        this.updateCartDisplay();
        this.updateCartSection();
    }

    updateCartDisplay() {
        const cartCount = document.querySelector('.cart-count');
        const totalItems = this.cart.reduce((sum, item) => sum + item.quantity, 0);
        cartCount.textContent = totalItems;
    }

    updateCartSection() {
        const cartContent = document.getElementById('cart-content');
        const orderFormContainer = document.getElementById('order-form-container');

        if (this.cart.length === 0) {
            cartContent.innerHTML = `
                <div class="cart-empty">
                    <i class="fas fa-shopping-cart"></i>
                    <h3>Votre panier est vide</h3>
                    <p>Ajoutez des menus pour commencer votre commande</p>
                    <button class="btn btn-primary nav-btn" data-section="menus">
                        <i class="fas fa-utensils"></i> Voir nos menus
                    </button>
                </div>
            `;
            orderFormContainer.innerHTML = '';
            return;
        }

        const totalAmount = this.cart.reduce((sum, item) => sum + (item.menu.price * item.quantity), 0);

        cartContent.innerHTML = `
            <div class="cart-items">
                ${this.cart.map(item => `
                    <div class="cart-item">
                        <div class="cart-item-info">
                            <h4>${item.menu.name}</h4>
                            <div class="cart-item-details">
                                ${item.menu.event_type} • ${item.menu.price.toFixed(2)}€ ${item.menu.serves}
                            </div>
                        </div>
                        <div class="quantity-controls">
                            <button class="quantity-btn" data-action="decrease" data-menu-id="${item.menu._id}">-</button>
                            <span class="quantity">${item.quantity}</span>
                            <button class="quantity-btn" data-action="increase" data-menu-id="${item.menu._id}">+</button>
                            <div class="item-total">${(item.menu.price * item.quantity).toFixed(2)}€</div>
                        </div>
                    </div>
                `).join('')}
            </div>
            <div class="cart-total">
                <h3>Total : ${totalAmount.toFixed(2)}€</h3>
            </div>
        `;

        this.renderOrderForm(totalAmount);
    }

    renderOrderForm(totalAmount) {
        const orderFormContainer = document.getElementById('order-form-container');
        
        orderFormContainer.innerHTML = `
            <h3><i class="fas fa-user-edit"></i> Vos informations de commande</h3>
            <form id="order-form">
                <div class="form-row">
                    <div class="form-group">
                        <label for="customer_name">Nom complet *</label>
                        <input type="text" id="customer_name" name="customer_name" required>
                    </div>
                    <div class="form-group">
                        <label for="customer_email">Email *</label>
                        <input type="email" id="customer_email" name="customer_email" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="customer_phone">Téléphone</label>
                        <input type="tel" id="customer_phone" name="customer_phone">
                    </div>
                    <div class="form-group">
                        <label for="delivery_date">Date de livraison souhaitée</label>
                        <input type="datetime-local" id="delivery_date" name="delivery_date">
                    </div>
                </div>
                <div class="form-group">
                    <label for="customer_address">Adresse de livraison</label>
                    <textarea id="customer_address" name="customer_address" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label for="notes">Notes particulières</label>
                    <textarea id="notes" name="notes" rows="3" placeholder="Allergies, préférences, instructions spéciales..."></textarea>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i> Envoyer la commande (${totalAmount.toFixed(2)}€)
                </button>
            </form>
        `;
    }

    async submitOrder() {
        const form = document.getElementById('order-form');
        const formData = new FormData(form);
        
        const orderData = {
            customer_name: formData.get('customer_name'),
            customer_email: formData.get('customer_email'),
            customer_phone: formData.get('customer_phone'),
            customer_address: formData.get('customer_address'),
            delivery_date: formData.get('delivery_date'),
            notes: formData.get('notes'),
            items: this.cart.map(item => ({
                menu_id: item.menu._id,
                menu_name: item.menu.name,
                menu_price: item.menu.price,
                quantity: item.quantity,
                subtotal: item.menu.price * item.quantity
            })),
            total_amount: this.cart.reduce((sum, item) => sum + (item.menu.price * item.quantity), 0)
        };

        this.showLoading(true);

        try {
            const response = await fetch('/api/orders.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(orderData)
            });

            const result = await response.json();
            
            if (result.success) {
                document.getElementById('order-number').textContent = result.order_id;
                document.getElementById('success-modal').classList.add('show');
                this.cart = [];
                this.updateCartDisplay();
                this.updateCartSection();
            } else {
                this.showError(result.message || 'Erreur lors de l\'envoi de la commande');
            }
        } catch (error) {
            console.error('Order submission error:', error);
            this.showError('Erreur de connexion');
        } finally {
            this.showLoading(false);
        }
    }

    closeModal(modalId) {
        document.getElementById(modalId).classList.remove('show');
    }

    showLoading(show) {
        const loading = document.getElementById('loading');
        if (show) {
            loading.classList.add('show');
        } else {
            loading.classList.remove('show');
        }
    }

    showNotification(message) {
        // Simple notification - could be enhanced with a proper toast system
        const notification = document.createElement('div');
        notification.className = 'notification';
        notification.textContent = message;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: #27ae60;
            color: white;
            padding: 1rem 2rem;
            border-radius: 10px;
            z-index: 1001;
            animation: slideIn 0.3s ease;
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }

    showError(message) {
        const notification = document.createElement('div');
        notification.className = 'notification error';
        notification.textContent = message;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: #e74c3c;
            color: white;
            padding: 1rem 2rem;
            border-radius: 10px;
            z-index: 1001;
            animation: slideIn 0.3s ease;
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 5000);
    }
}

// Initialize the application when the page loads
document.addEventListener('DOMContentLoaded', () => {
    new ViteGourmandApp();
});