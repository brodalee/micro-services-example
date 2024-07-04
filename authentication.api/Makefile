.PHONY: deploy-prod
deploy-prod:
	@cd ansible && \
	ansible-galaxy collection install --force -r requirements.yml && \
	ansible-playbook site.yml --inventory inventories/prod --verbose


.PHONY: docker-build-image-php
docker-build-image-php:
	@docker buildx build . -t ms/auth:phpfpm

.PHONY: docker-build-image-nginx
docker-build-image-nginx:
	@docker buildx build ./docker/nginx -t ms/auth:nginx

#  && \
  #	docker buildx build . -t ms:auth:nginx