.PHONY: push zip

push:
	git push origin main && git push origin --tags

zip:
	@echo "Creating infinity_cms.zip..."
	@rm -f infinity_cms.zip
	@zip -r infinity_cms.zip . \
		-x ".git/*" \
		-x "config.php" \
		-x "compose.development.yml" \
		-x "Dockerfile.development" \
		-x "public/assets/*" \
		-x "storage/logs/*" \
		-x "*.DS_Store" \
		-x "infinity_cms.zip"
	@zip -u infinity_cms.zip storage/logs/.gitignore
	@echo "Done! Created infinity_cms.zip ✓"