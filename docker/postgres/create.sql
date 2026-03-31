CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    mdp TEXT NOT NULL, -- À stocker en mode hashé !
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE categories (
    id SERIAL PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL -- Pour l'URL rewriting (ex: /guerre-iran/politique)
);
CREATE TABLE articles (
    id SERIAL PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL, -- Utilisé pour les URL propres
    resume TEXT,
    contenu TEXT NOT NULL,
    image_url TEXT,
    image_alt VARCHAR(150), -- Important pour ton point SEO (balise alt)
    meta_title VARCHAR(70),  -- Pour le SEO
    meta_description VARCHAR(160), -- Pour le SEO
    date_publication TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_published BOOLEAN DEFAULT FALSE,
    is_featured  BOOLEAN DEFAULT FALSE,
    category_id INTEGER REFERENCES categories(id) ON DELETE SET NULL,
    author_id INTEGER REFERENCES users(id)
);