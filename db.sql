-- =========================================
-- DATABASE SETTINGS
-- =========================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- =========================================
-- ADMINS
-- =========================================

CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    last_login TIMESTAMP NULL DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================
-- USERS
-- =========================================

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,

    fullname VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,

    dob DATE DEFAULT NULL,

    phone_number VARCHAR(20) DEFAULT NULL,
    whatsapp_number VARCHAR(20) DEFAULT NULL,

    pin VARCHAR(6) DEFAULT NULL,
    withdrawal_pin VARCHAR(6) DEFAULT NULL,

    account_balance DECIMAL(18,2) DEFAULT 0.00,
    investment_balance DECIMAL(18,2) DEFAULT 0.00,
    profit_balance DECIMAL(18,2) DEFAULT 0.00,
    trading_balance DECIMAL(18,2) DEFAULT 0.00,
    secure_vault_balance DECIMAL(18,2) DEFAULT 0.00,

    currency VARCHAR(10) DEFAULT 'USD',
    rank VARCHAR(50) DEFAULT 'Beginner',

    trader_account VARCHAR(100) DEFAULT NULL,

    admin_remark TEXT DEFAULT NULL,

    status ENUM('active','suspended','pending') DEFAULT 'active',

    pin_status TINYINT(1) DEFAULT 0,

    webauthn_id VARCHAR(255) UNIQUE DEFAULT NULL,
    two_factor_enabled TINYINT(1) DEFAULT 0,

    last_login TIMESTAMP NULL DEFAULT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================
-- INVESTMENT PLANS
-- =========================================

CREATE TABLE inv_plans (
    id INT AUTO_INCREMENT PRIMARY KEY,

    plan_name VARCHAR(100) NOT NULL,

    plan_description TEXT DEFAULT NULL,

    min_amount DECIMAL(18,2) NOT NULL,
    max_amount DECIMAL(18,2) NOT NULL,

    roi_percentage DECIMAL(5,2) DEFAULT NULL,
    roi_amount DECIMAL(18,2) DEFAULT NULL,

    duration_days INT NOT NULL,

    status ENUM('active','inactive') DEFAULT 'active',

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================
-- INVESTMENTS
-- =========================================

CREATE TABLE investments (
    id INT AUTO_INCREMENT PRIMARY KEY,

    user_id INT NOT NULL,
    plan_id INT NOT NULL,

    amount DECIMAL(18,2) NOT NULL,

    returns_accrued DECIMAL(18,2) DEFAULT 0.00,
    returns_deposited DECIMAL(18,2) DEFAULT 0.00,

    start_date TIMESTAMP NOT NULL,
    end_date TIMESTAMP NOT NULL,

    status ENUM('running','completed','cancelled') DEFAULT 'running',

    admin_remark TEXT DEFAULT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_investments_user
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_investments_plan
        FOREIGN KEY (plan_id)
        REFERENCES inv_plans(id)
        ON DELETE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================
-- TRADABLE ASSETS
-- =========================================

CREATE TABLE tradable_assets (
    id INT AUTO_INCREMENT PRIMARY KEY,

    symbol VARCHAR(20) NOT NULL UNIQUE,
    asset_name VARCHAR(100) NOT NULL,

    asset_type ENUM('stock','crypto','forex','commodity') DEFAULT 'crypto',

    current_price DECIMAL(18,8) DEFAULT 0.00000000,

    is_active TINYINT(1) DEFAULT 1,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================
-- ASSET INVESTMENTS
-- =========================================

CREATE TABLE asset_investments (
    id INT AUTO_INCREMENT PRIMARY KEY,

    user_id INT NOT NULL,
    asset_id INT NOT NULL,

    units DECIMAL(18,8) NOT NULL,
    purchase_price DECIMAL(18,8) NOT NULL,

    total_cost DECIMAL(18,2) NOT NULL,

    trade_type ENUM('buy','sell') NOT NULL,

    status ENUM('open','closed') DEFAULT 'open',

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_asset_user
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_asset_asset
        FOREIGN KEY (asset_id)
        REFERENCES tradable_assets(id)
        ON DELETE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================
-- DEPOSITS
-- =========================================

CREATE TABLE deposits (
    id INT AUTO_INCREMENT PRIMARY KEY,

    user_id INT NOT NULL,

    amount DECIMAL(18,2) NOT NULL,

    method_details TEXT DEFAULT NULL,

    proof_image_url VARCHAR(255) DEFAULT NULL,

    status ENUM('pending','approved','rejected') DEFAULT 'pending',

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_deposit_user
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================
-- WITHDRAWALS
-- =========================================

CREATE TABLE withdrawals (
    id INT AUTO_INCREMENT PRIMARY KEY,

    user_id INT NOT NULL,

    amount DECIMAL(18,2) NOT NULL,

    method_name TEXT DEFAULT NULL,

    status ENUM('pending','approved','rejected') DEFAULT 'pending',

    admin_remark TEXT DEFAULT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_withdrawal_user
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================
-- TRANSACTIONS
-- =========================================

CREATE TABLE transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,

    user_id INT NOT NULL,

    amount DECIMAL(18,2) NOT NULL,

    type VARCHAR(50) NOT NULL,

    description TEXT DEFAULT NULL,

    running_balance DECIMAL(18,2) DEFAULT 0.00,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_transaction_user
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================
-- KYC
-- =========================================

CREATE TABLE kyc (
    id INT AUTO_INCREMENT PRIMARY KEY,

    user_id INT NOT NULL,

    document_type VARCHAR(50) DEFAULT NULL,

    id_images TEXT DEFAULT NULL,

    status ENUM('pending','verified','rejected') DEFAULT 'pending',

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_kyc_user
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================
-- NOTIFICATIONS
-- =========================================

CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,

    user_id INT NOT NULL,

    title VARCHAR(255) NOT NULL,

    message TEXT NOT NULL,

    is_read TINYINT(1) DEFAULT 0,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_notification_user
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================
-- SITE SETTINGS
-- =========================================

CREATE TABLE site_settings (
    id INT PRIMARY KEY DEFAULT 1,

    site_name VARCHAR(100) DEFAULT NULL,
    email VARCHAR(150) DEFAULT NULL,
    phone VARCHAR(20) DEFAULT NULL,

    address TEXT DEFAULT NULL,

    logo_url VARCHAR(255) DEFAULT NULL,

    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================
-- PAYMENT METHODS
-- =========================================

CREATE TABLE payment_methods (
    id INT AUTO_INCREMENT PRIMARY KEY,

    name VARCHAR(100) NOT NULL,

    min_deposit DECIMAL(18,2) DEFAULT 0.00,
    max_deposit DECIMAL(18,2) DEFAULT 0.00,

    wallet_address VARCHAR(255) DEFAULT NULL,
    
    qr_code_url VARCHAR(255) DEFAULT NULL,
     
    extra_details TEXT DEFAULT NULL,

    status ENUM('active','inactive') DEFAULT 'active',

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================
-- PASSWORD RESETS
-- =========================================

CREATE TABLE password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,

    email VARCHAR(150) NOT NULL,

    token VARCHAR(255) NOT NULL,

    expires_at TIMESTAMP NOT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================
-- USER PASSKEYS
-- =========================================

CREATE TABLE user_passkeys (
    id INT AUTO_INCREMENT PRIMARY KEY,

    user_id INT NOT NULL,

    credential_id TEXT NOT NULL,
    public_key LONGTEXT NOT NULL,

    counter BIGINT DEFAULT 0,

    device_type VARCHAR(100) DEFAULT NULL,

    transports TEXT DEFAULT NULL,

    last_used_at TIMESTAMP NULL DEFAULT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT uq_credential_id UNIQUE (credential_id(255)),

    CONSTRAINT fk_passkey_user
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

COMMIT;