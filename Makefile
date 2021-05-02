.PHONY: build
build: resources/cv.json

resources/cv.json: $(wildcard cv/cv.json) resources/cv.sample.json
	if [ -f cv/cv.json ]; then cp -p cv/cv.json resources/cv.json; else cp -p resources/cv.sample.json resources/cv.json; fi
