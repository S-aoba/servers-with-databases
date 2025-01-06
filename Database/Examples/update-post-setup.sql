ALTER TABLE posts 
ADD COLUMN category_id INT NOT NULL,
ADD CONSTRAINT fk_category
FOREIGN KEY (category_id) REFERENCES categories(id);
