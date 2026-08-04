# Used by docker-compose.yml (production) only. Builds the React panel and
# serves it, plus the backend's static backoffice assets (css/js/media),
# directly -- there's no separate "frontend" container in production and
# nothing here is bind-mounted from the host. docker-compose.dev.yml keeps
# using the plain nginx:stable-alpine image with the source bind-mounted,
# since that's what hot reload needs; this image is not used there.
#
# Build context is the repository root (see docker-compose.yml) so this can
# reach both frontend/ and backend/public/ in one build.

FROM node:20-alpine AS frontend_build
WORKDIR /app
COPY frontend/package.json frontend/yarn.lock ./
RUN yarn install --frozen-lockfile
COPY frontend/ .
ARG VITE_API_BASE=/api
ENV VITE_API_BASE=${VITE_API_BASE}
RUN yarn build

FROM nginx:stable-alpine
COPY --from=frontend_build /app/dist /var/www/frontend/dist
COPY backend/public/css /var/www/backend/public/css
COPY backend/public/js /var/www/backend/public/js
COPY backend/public/media /var/www/backend/public/media
COPY .development/nginx/default.conf /etc/nginx/conf.d/default.conf
