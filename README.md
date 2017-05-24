# README

## Installation

```sh
# Install dependencies

composer install
```

## Usage

```sh
# Define your database credentials and API entrypoint

cat > .env << "EOF"
API_ENTRYPOINT="127.0.0.1:8000"

DATABASE_HOST="mysql-morgan.docker"
DATABASE_USER="root"
DATABASE_NAME="mds"
EOF

bin/joomla-extractor ping:api
# Ping OK

bin/joomla-extractor ping:database
# Ping OK

# Extract resources

bin/joomla-extract extract:job
# Normalize extracted jobs...
#  3/3 [============================] 100%
# Normalization completed
# ---
# Send people to API...
#  3/3 [============================] 100%
# Sending completed

bin/joomla-extract extract:person
# Normalize extracted people...
#  242/242 [============================] 100%
# Normalization completed
# ---
# Send people to API...
#  242/242 [============================] 100%
# Sending completed

bin/joomla-extract extract:tv_series
# Normalize extracted TV series...
#  63/63 [============================] 100%
# Normalize completed
# ---
# Send TV series to API...
#  63/63 [============================] 100%
# Sending completed
```

## Contributing

1. Fork it.
2. Create your branch: `git checkout -b my-new-feature`.
3. Commit your changes: `git commit -am 'Add some feature'`.
4. Push to the branch: `git push origin my-new-feature`.
5. Submit a pull request.
