import { ref, computed, onMounted, onUnmounted, watch } from "vue";

export function useLocalStorageSaver(form, localStorageKeyId, formFields, keyPrefix = 'edit-draft', imageFields = ['image_file']) {
    const localStorageKey = computed(() => `${keyPrefix}-${localStorageKeyId}`);
    const isSavedToLocalStorage = ref(false);

    const hasFormContent = computed(() => {
        return formFields.some(field => {
            const value = form[field];
            if (Array.isArray(value)) {
                return value.length > 0;
            }
            if (typeof value === 'string') {
                return value.trim() !== '';
            }
            return value !== null && value !== undefined;
        });
    });

    const saveToLocalStorage = () => {
        if (hasFormContent.value) {
            const formData = {};
            const promises = [];
            formFields.forEach(field => {
                if (imageFields.includes(field) && form[field] instanceof File) {
                    // Convert image file to base64 string
                    const file = form[field];
                    const reader = new FileReader();
                    const promise = new Promise((resolve) => {
                        reader.onload = (e) => {
                            formData[field] = {
                                name: file.name,
                                type: file.type,
                                dataUrl: e.target.result
                            };
                            resolve();
                        };
                        reader.readAsDataURL(file);
                    });
                    promises.push(promise);
                } else {
                    formData[field] = form[field];
                }
            });
            Promise.all(promises).then(() => {
                formData.savedAt = new Date().toISOString();
                localStorage.setItem(localStorageKey.value, JSON.stringify(formData));
                isSavedToLocalStorage.value = true;
            });
            if (promises.length === 0) {
                formData.savedAt = new Date().toISOString();
                localStorage.setItem(localStorageKey.value, JSON.stringify(formData));
                isSavedToLocalStorage.value = true;
            }
        } else {
            localStorage.removeItem(localStorageKey.value);
            isSavedToLocalStorage.value = false;
        }
    };

    const loadFromLocalStorage = () => {
        const savedData = localStorage.getItem(localStorageKey.value);
        if (savedData) {
            try {
                const parsedData = JSON.parse(savedData);
                formFields.forEach(field => {
                    if (parsedData[field] !== undefined) {
                        if (imageFields.includes(field) && parsedData[field] && parsedData[field].dataUrl) {
                            // Restore as a File object if possible, otherwise as dataUrl
                            try {
                                const { name, type, dataUrl } = parsedData[field];
                                // Convert dataUrl to Blob
                                const arr = dataUrl.split(','), mime = arr[0].match(/:(.*?);/)[1], bstr = atob(arr[1]), n = bstr.length, u8arr = new Uint8Array(n);
                                for (let i = 0; i < n; i++) {
                                    u8arr[i] = bstr.charCodeAt(i);
                                }
                                form[field] = new File([u8arr], name, { type: mime });
                            } catch (e) {
                                form[field] = parsedData[field].dataUrl;
                            }
                        } else {
                            form[field] = parsedData[field];
                        }
                    }
                });
                isSavedToLocalStorage.value = true;
            } catch (error) {
                console.error('Error loading saved data:', error);
                localStorage.removeItem(localStorageKey.value);
                isSavedToLocalStorage.value = false;
            }
        }
    };

    let saveTimeout;
    watch(form, () => {
        clearTimeout(saveTimeout);
        saveTimeout = setTimeout(() => {
            saveToLocalStorage();
        }, 500);
    }, { deep: true });

    onMounted(() => {
        loadFromLocalStorage();
    });

    onUnmounted(() => {
        clearTimeout(saveTimeout);
    });

    const clearLocalStorage = () => {
        localStorage.removeItem(localStorageKey.value);
        isSavedToLocalStorage.value = false;
    };

    return {
        isSavedToLocalStorage,
        hasFormContent,
        clearLocalStorage
    };
}
