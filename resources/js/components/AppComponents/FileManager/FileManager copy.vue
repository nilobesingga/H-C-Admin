<template>
     <div class="px-6 py-6 container-fluid">
        <div class="grid gap-2">
            <!-- filters -->
            <div class="flex items-center justify-between gap-2">
                <div class="flex">
                    <select class="select select-sm select-input w-96" v-model="filters.sage_company_code">
                        <option value="" selected>Filter by Sage Company</option>
                        <option v-for="obj in companies" :key="obj.sageDBCode" :value="obj.sageDBCode">
                            {{ obj.companyName }}
                        </option>
                    </select>
                </div>
                <div class="flex grow">
                    <div class="relative w-full">
                        <i class="absolute leading-none text-black transform -translate-y-1/2 ki-outline ki-magnifier text-md top-1/2 left-3"></i>
                        <input
                            class="input input-sm text-input !ps-8"
                            placeholder="Search"
                            type="text"
                            v-model="filters.search"
                        />
                    </div>
                </div>

            <!-- Sort Controls -->
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <select class="select select-sm select-input" v-model="sortBy" style="width: 120px">
                        <option value="name">Name</option>
                        <option value="size">Size</option>
                        <option value="last_modified">Date Modified</option>
                        <option value="type">Type</option>
                    </select>
                    <button class="btn btn-icon btn-sm relative px-3 h-10 !w-10 !rounded-none transition-all duration-300 hover:border-black btn-light text-black" @click="toggleSortDirection">
                        <svg v-if="sortDirection === 'asc'" xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-sort-ascending" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M4 6l7 0"></path>
                            <path d="M4 12l7 0"></path>
                            <path d="M4 18l9 0"></path>
                            <path d="M15 9l3 -3l3 3"></path>
                            <path d="M18 6l0 12"></path>
                        </svg>
                        <svg v-else xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-sort-descending" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M4 6l9 0"></path>
                            <path d="M4 12l7 0"></path>
                            <path d="M4 18l7 0"></path>
                            <path d="M15 15l3 3l3 -3"></path>
                            <path d="M18 6l0 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="flex items-center">
                    <button class="btn btn-icon btn-sm relative px-3 h-10 !w-10 !rounded-none transition-all duration-300 hover:border-black btn-light text-black" @click="refreshFiles">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-refresh" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4"></path>
                            <path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>


            <div class="relative flex-grow px-3 overflow-auto border shadow-md bank-summary-table-container border-brand" tabindex="0" @keydown="handleKeyDown">
                <!-- Loading Indicator -->
                <div v-if="isLoading || isSearching" class="absolute inset-0 flex items-center justify-center pointer-events-none data-loading bg-neutral-100 z-100">
                    <div class="flex items-center gap-2 px-4 py-2 text-sm font-medium leading-none text-brand-active">
                        <svg class="w-5 h-5 -ml-1 animate-spin text-brand-active" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Loading...
                    </div>
                </div>

                <!-- Search Results Panel -->
                <div v-if="!isLoading && !error && searchResults.length > 0 && showSearchResultsPanel" class="search-results-panel">
                    <div class="search-results-header">
                        <div class="flex items-center justify-between mb-3">
                            <h5 class="flex items-center text-lg font-semibold">
                                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 icon icon-tabler icon-tabler-search" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0"></path>
                                    <path d="M21 21l-6 -6"></path>
                                </svg>
                                Search Results ({{ searchResults.length }})
                                <span class="ml-2 text-sm font-normal text-gray-600">for "{{ filters.search }}"</span>
                            </h5>
                            <div class="flex items-center">
                                <select v-model="searchResultsSort" class="mr-2 select select-sm select-input" style="width: 140px">
                                    <option value="name">Sort by name</option>
                                    <option value="path">Sort by path</option>
                                    <option value="type">Sort by type</option>
                                </select>
                                <button @click="clearSearchResults" class="btn btn-sm btn-outline-secondary">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-x" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M18 6l-12 12"></path>
                                        <path d="M6 6l12 12"></path>
                                    </svg>
                                    Close
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="search-results-content">
                        <div class="list-group">
                            <div v-for="result in sortedSearchResults" :key="result.path"
                                class="list-group-item search-result-item"
                                @click="navigateToSearchResult(result)">
                                <div class="flex items-center">
                                    <div class="mr-3 file-type-icon">
                                        <file-icon v-if="result.type === 'file'" :extension="result.extension" />
                                        <svg v-else xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-folder text-warning" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M5 4h4l3 3h7a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-11a2 2 0 0 1 2 -2"></path>
                                        </svg>
                                    </div>
                                    <div class="file-info grow">
                                        <div class="font-medium file-name" v-html="highlightMatch(result.name, filters.search)"></div>
                                        <div class="text-sm text-gray-500 file-path">{{ formatPathForDisplay(result.path) }}</div>
                                        <div v-if="result.type === 'file' && result.size" class="mt-1 text-xs text-gray-400 file-meta">
                                            {{ formatFileSize(result.size) }} · {{ result.extension ? result.extension.toUpperCase() : 'File' }}
                                        </div>
                                    </div>
                                    <div class="file-actions">
                                        <button v-if="result.type === 'file'" class="btn btn-sm btn-icon btn-outline-primary" @click.stop="downloadFile(result)" title="Download">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-download" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2"></path>
                                                <path d="M7 11l5 5l5 -5"></path>
                                                <path d="M12 4l0 12"></path>
                                            </svg>
                                        </button>
                                        <button v-if="result.type === 'dir'" class="btn btn-sm btn-icon btn-outline-secondary" @click.stop="openDirectory(result.path)" title="Open">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-folder-open" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                <path d="M5 4h4l3 3h7a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-11a2 2 0 0 1 2 -2"></path>
                                                <path d="M6 10v8"></path>
                                                <path d="M7 10v8"></path>
                                                <path d="M8.5 10v8"></path>
                                                <path d="M11 10v8"></path>
                                                <path d="M14 10v8"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- File browser - Tree View -->
                <div v-if="!isLoading && !error && viewMode === 'treeview' && (!searchResults.length || !showSearchResultsPanel)"
                    class="file-manager-treeview-container"
                    @dragover.prevent="handleDragOver"
                    @dragleave.prevent="handleDragLeave"
                    @drop.prevent="handleFileDrop">

                    <!-- Drag overlay -->
                    <div v-if="isDragging" class="file-drop-overlay">
                        <div class="file-drop-message">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-upload" width="48" height="48" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" />
                                <path d="M7 9l5 -5l5 5" />
                                <path d="M12 4l0 12" />
                            </svg>
                            <h3>Drop files here to upload</h3>
                            <p>Files will be uploaded to {{ formatPathDisplay(currentPath) }}</p>
                        </div>
                    </div>

                    <!-- Upload progress overlay -->
                    <div v-if="isUploading" class="upload-progress-overlay">
                        <div class="upload-progress-container">
                            <div class="upload-progress-header">
                                <h4>Uploading {{ uploadQueue.length }} file(s)</h4>
                                <button @click="cancelAllUploads" class="btn btn-sm btn-outline-danger">Cancel All</button>
                            </div>
                            <div class="upload-items">
                                <div v-for="(file, index) in uploadQueue" :key="index" class="upload-item">
                                    <div class="upload-item-info">
                                        <span class="upload-item-name">{{ file.name }}</span>
                                        <span class="upload-item-size">{{ formatFileSize(file.size) }}</span>
                                    </div>
                                    <div class="upload-item-progress">
                                        <div class="progress">
                                            <div class="progress-bar"
                                                 :class="{
                                                    'bg-primary': file.status === 'uploading',
                                                    'bg-success': file.status === 'completed',
                                                    'bg-danger': file.status === 'error',
                                                    'bg-secondary': file.status === 'cancelled'
                                                 }"
                                                 :style="{ width: file.progress + '%' }"></div>
                                        </div>
                                        <div v-if="file.status === 'uploading'" class="upload-status">
                                            {{ file.progress }}%
                                            <button @click="cancelUpload(index)" class="ml-2 btn btn-xs btn-icon btn-light">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-x" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                    <path d="M18 6l-12 12" />
                                                    <path d="M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                        <div v-else-if="file.status === 'completed'" class="upload-completed">
                                            Complete
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-circle-check" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                                <path d="M9 12l2 2l4 -4" />
                                            </svg>
                                        </div>
                                        <div v-else-if="file.status === 'error'" class="upload-error">
                                            Error: {{ file.error }}
                                        </div>
                                        <div v-else-if="file.status === 'cancelled'" class="upload-cancelled">
                                            Cancelled
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="upload-actions">
                                <button v-if="allUploadsCompleted" @click="closeUploadProgress" class="btn btn-sm btn-primary">Done</button>
                            </div>
                        </div>
                    </div>

                    <div>
                        <div class="mb-3 upload-actions-bar">
                            <button class="btn btn-sm btn-primary" @click="triggerFileUpload">
                                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 icon icon-tabler icon-tabler-upload" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" />
                                    <path d="M7 9l5 -5l5 5" />
                                    <path d="M12 4l0 12" />
                                </svg>
                                Upload Files
                            </button>
                            <input type="file" ref="fileInput" multiple @change="handleFileSelect" class="hidden-file-input" />
                            <span class="ml-3 text-sm text-gray-500">or drag and drop files here</span>
                        </div>
                        <div class="p-0">
                            <div class="file-manager-treeview" ref="treeViewContainer">
                                <div v-if="filteredTreeViewData.length > 0">
                                    <folder-tree-node
                                        v-for="node in filteredTreeViewData"
                                        :key="node.path"
                                        :node="node"
                                        :level="0"
                                        @node-click="handleTreeNodeClick"
                                        @node-right-click="handleContextMenu"
                                        @file-click="fileInfo"
                                        @file-right-click="handleContextMenu"
                                        @download="downloadFile"
                                        :current-path="currentPath" />
                                </div>
                                <div v-else class="py-5 text-center">
                                    <p class="mt-3 text-muted">No items found</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- File browser - Grid View -->
                <div v-if="!isLoading && !error && viewMode === 'grid'" class="file-manager-grid">
                    <!-- Directories -->
                    <div v-if="directories.length > 0" class="mb-3">
                        <h6 class="mb-2 text-muted">Folders</h6>
                        <div class="row g-3">
                            <div v-for="directory in directories" :key="directory.path"
                                class="col-md-3 col-sm-4 col-6">
                                <div class="card folder-card" @click="openDirectory(directory.path)">
                                    <div class="p-3 card-body">
                                        <div class="d-flex align-items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-folder text-warning me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                <path d="M5 4h4l3 3h7a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-11a2 2 0 0 1 2 -2"></path>
                                            </svg>
                                            <div class="folder-name text-truncate">{{ directory.name }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Files -->
                    <div v-if="files.length > 0" class="mb-3">
                        <h6 class="mb-2 text-muted">Files</h6>
                        <div class="row g-3">
                            <div v-for="file in files" :key="file.path"
                                class="col-md-3 col-sm-4 col-6">
                                <div class="card file-card">
                                    <div class="p-3 card-body">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="overflow-hidden d-flex align-items-center flex-grow-1" @click="fileInfo(file)">
                                                <div class="file-icon me-2">
                                                    <file-icon :extension="file.extension" />
                                                </div>
                                                <div class="file-name text-truncate">{{ file.name }}</div>
                                            </div>
                                            <div class="file-actions ms-2">
                                                <button class="btn btn-sm btn-icon btn-outline-primary btn-group" @click.stop="downloadFile(file)" title="Download">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-download" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                        <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2"></path>
                                                        <path d="M7 11l5 5l5 -5"></path>
                                                        <path d="M12 4l0 12"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Empty state -->
                    <div v-if="directories.length === 0 && files.length === 0" class="py-5 text-center empty-folder-state">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-folder-off" width="48" height="48" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M8 4h1l3 3h7a2 2 0 0 1 2 2v8m-2 2h-14a2 2 0 0 1 -2 -2v-11a2 2 0 0 1 1.189 -1.829"></path>
                            <path d="M3 3l18 18"></path>
                        </svg>
                        <p class="mt-3 text-muted">This folder is empty</p>
                    </div>
                </div>

                <!-- Context Menu -->
                <div class="context-menu" ref="contextMenu" v-show="contextMenuVisible" :style="contextMenuStyle">
                    <div class="list-group">
                        <button class="list-group-item list-group-item-action d-flex align-items-center" v-if="contextMenuTarget && contextMenuTarget.type === 'dir'" @click="openContextMenuTarget">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-folder-open me-2" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M5 4h4l3 3h7a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-11a2 2 0 0 1 2 -2"></path>
                                <path d="M10 10l2 -2"></path>
                                <path d="M13 13l-2 2"></path>
                            </svg>
                            Open Folder
                        </button>
                        <button class="list-group-item list-group-item-action d-flex align-items-center" v-if="contextMenuTarget && contextMenuTarget.type === 'file'" @click="fileInfo(contextMenuTarget)">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-file-info me-2" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M14 3v4a1 1 0 0 0 1 1h4"></path>
                                <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"></path>
                                <path d="M11 14h1v4h1"></path>
                                <path d="M12 11h.01"></path>
                            </svg>
                            File Info
                        </button>
                        <button class="list-group-item list-group-item-action d-flex align-items-center" v-if="contextMenuTarget && contextMenuTarget.type === 'file'" @click="downloadFile(contextMenuTarget)">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-download me-2" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2"></path>
                                <path d="M7 11l5 5l5 -5"></path>
                                <path d="M12 4l0 12"></path>
                            </svg>
                            Download
                        </button>
                        <button class="list-group-item list-group-item-action d-flex align-items-center" v-if="canPreviewFile(contextMenuTarget)" @click="previewFile(contextMenuTarget)">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-eye me-2" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"></path>
                                <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6"></path>
                            </svg>
                            Preview
                        </button>
                    </div>
                </div>

                <!-- File info modal -->
                <div class="modal fade" id="fileInfoModal" tabindex="-1" aria-labelledby="fileInfoModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="fileInfoModalLabel">
                                    <span class="d-flex align-items-center">
                                        <file-icon v-if="selectedFile" :extension="selectedFile.extension" class="me-2" />
                                        File Information
                                    </span>
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body" v-if="selectedFile">
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <th>Name</th>
                                            <td>{{ selectedFile.name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Path</th>
                                            <td>{{ selectedFile.path }}</td>
                                        </tr>
                                        <tr>
                                            <th>Size</th>
                                            <td>{{ formatFileSize(selectedFile.size) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Last Modified</th>
                                            <td>{{ formatDate(selectedFile.last_modified) }}</td>
                                        </tr>
                                        <tr v-if="selectedFile.extension">
                                            <th>Type</th>
                                            <td>{{ selectedFile.extension.toUpperCase() }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-primary" @click="downloadFile(selectedFile)">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-download me-1" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2"></path>
                                        <path d="M7 11l5 5l5 -5"></path>
                                        <path d="M12 4l0 12"></path>
                                    </svg>
                                    Download
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- File Preview Modal -->
                <div class="modal fade" id="filePreviewModal" tabindex="-1" aria-labelledby="filePreviewModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="filePreviewModalLabel">
                                    <span class="d-flex align-items-center">
                                        <file-icon v-if="previewingFile" :extension="previewingFile.extension" class="me-2" />
                                        {{ previewingFile ? previewingFile.name : 'File Preview' }}
                                    </span>
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="p-0 modal-body" v-if="previewingFile">
                                <!-- Image Preview -->
                                <div v-if="isImageFile(previewingFile)" class="p-3 text-center">
                                    <img :src="getPreviewUrl(previewingFile)" class="img-fluid" :alt="previewingFile.name">
                                </div>

                                <!-- PDF Preview -->
                                <div v-else-if="previewingFile.extension === 'pdf'" class="pdf-container">
                                    <iframe :src="getPreviewUrl(previewingFile)" class="w-100" style="height: 70vh;"></iframe>
                                </div>

                                <!-- Text Preview -->
                                <div v-else-if="isTextFile(previewingFile)" class="p-3">
                                    <div v-if="filePreviewContent" class="text-preview">
                                        <pre>{{ filePreviewContent }}</pre>
                                    </div>
                                    <div v-else class="py-5 text-center">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Unsupported Format -->
                                <div v-else class="p-5 text-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="mb-3 icon icon-tabler icon-tabler-file-x" width="64" height="64" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M14 3v4a1 1 0 0 0 1 1h4"></path>
                                        <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"></path>
                                        <path d="M10 12l4 4m0 -4l-4 4"></path>
                                    </svg>
                                    <h4>Preview Not Available</h4>
                                    <p class="text-muted">This file type cannot be previewed directly in the browser.</p>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-primary" @click="downloadFile(previewingFile)">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-download me-1" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2"></path>
                                        <path d="M7 11l5 5l5 -5"></path>
                                        <path d="M12 4l0 12"></path>
                                    </svg>
                                    Download
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Search Modal -->
                <div class="modal fade" id="searchModal" tabindex="-1" aria-labelledby="searchModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="searchModalLabel">Search Files</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <div class="input-group">
                                        <input type="text" class="form-control" placeholder="Enter file name..." v-model="searchQuery" @keyup.enter="performSearch">
                                        <button class="btn btn-primary" type="button" @click="performSearch">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-search" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0"></path>
                                                <path d="M21 21l-6 -6"></path>
                                            </svg>
                                            Search
                                        </button>
                                    </div>
                                </div>

                                <div v-if="isSearching" class="py-3 text-center">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Searching...</span>
                                    </div>
                                    <p class="mt-2">Searching for files...</p>
                                </div>

                                <div v-else-if="searchResults.length > 0" class="search-results">
                                    <h6>Search Results ({{ searchResults.length }})</h6>
                                    <div class="list-group">
                                        <a href="#" v-for="result in searchResults" :key="result.path" @click.prevent="navigateToSearchResult(result)" class="list-group-item list-group-item-action">
                                            <div class="d-flex align-items-center">
                                                <file-icon v-if="result.type === 'file'" :extension="result.extension" class="me-2" />
                                                <svg v-else xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-folder text-warning me-2" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                    <path d="M5 4h4l3 3h7a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-11a2 2 0 0 1 2 -2"></path>
                                                </svg>
                                                <div>
                                                    <div>{{ result.name }}</div>
                                                    <small class="text-muted">{{ result.path }}</small>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>

                                <div v-else-if="searchPerformed" class="py-4 text-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="mb-2 icon icon-tabler icon-tabler-file-search" width="48" height="48" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M14 3v4a1 1 0 0 0 1 1h4"></path>
                                        <path d="M12 21h-5a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v4.5"></path>
                                        <path d="M16.5 17.5m-2.5 0a2.5 2.5 0 1 0 5 0a2.5 2.5 0 1 0 -5 0"></path>
                                        <path d="M18.5 19.5l2.5 2.5"></path>
                                    </svg>
                                    <p class="text-muted">No results found for "{{ searchQuery }}"</p>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
    </div>
</div>
</template>

<script>
import axios from 'axios';
import {DateTime} from "luxon";
import _ from "lodash";
// Import Bootstrap JS
// import * as bootstrap from 'bootstrap';

// Component for displaying file type icons
const FileIcon = {
    props: ['extension'],
    render(h) {
        let iconSvg;

        if (this.extension === 'pdf') {
            iconSvg = `
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-file-type-pdf text-danger" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M14 3v4a1 1 0 0 0 1 1h4"></path>
                    <path d="M5 12v-7a2 2 0 0 1 2 -2h7l5 5v4"></path>
                    <path d="M5 18h1.5a1.5 1.5 0 0 0 0 -3h-1.5v6"></path>
                    <path d="M17 18h2"></path>
                    <path d="M20 15h-3v6"></path>
                    <path d="M11 15v6h1a2 2 0 0 0 2 -2v-2a2 2 0 0 0 -2 -2h-1z"></path>
                </svg>
            `;
        } else if (['doc', 'docx'].includes(this.extension)) {
            iconSvg = `
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-file-text text-primary" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M14 3v4a1 1 0 0 0 1 1h4"></path>
                    <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"></path>
                    <path d="M9 9l1 0"></path>
                    <path d="M9 13l6 0"></path>
                    <path d="M9 17l6 0"></path>
                </svg>
            `;
        } else if (['xls', 'xlsx', 'csv'].includes(this.extension)) {
            iconSvg = `
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-file-spreadsheet text-success" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M14 3v4a1 1 0 0 0 1 1h4"></path>
                    <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"></path>
                    <path d="M8 11h8v7h-8z"></path>
                    <path d="M8 15h8"></path>
                    <path d="M11 11v7"></path>
                </svg>
            `;
        } else if (['jpg', 'jpeg', 'png', 'gif'].includes(this.extension)) {
            iconSvg = `
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-photo text-info" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M15 8h.01"></path>
                    <path d="M3 6a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v12a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3v-12z"></path>
                    <path d="M3 16l5 -5c.928 -.893 2.072 -.893 3 0l5 5"></path>
                    <path d="M14 14l1 -1c.928 -.893 2.072 -.893 3 0l3 3"></path>
                </svg>
            `;
        } else if (['zip', 'rar', 'tar', 'gz'].includes(this.extension)) {
            iconSvg = `
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-file-zip text-secondary" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M6 20.735a2 2 0 0 1 -1 -1.735v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2h-1"></path>
                    <path d="M11 17a2 2 0 0 1 2 2v2a1 1 0 0 1 -1 1h-2a1 1 0 0 1 -1 -1v-2a2 2 0 0 1 2 -2z"></path>
                    <path d="M11 5l-1 0"></path>
                    <path d="M11 8l-1 0"></path>
                    <path d="M11 11l-1 0"></path>
                    <path d="M11 14l-1 0"></path>
                </svg>
            `;
        } else {
            iconSvg = `
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-file" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M14 3v4a1 1 0 0 0 1 1h4"></path>
                    <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"></path>
                </svg>
            `;
        }

        return h('div', {
            domProps: {
                innerHTML: iconSvg
            }
        });
    }
};

// Recursive component for folder tree
const FolderTreeNode = {
    name: 'FolderTreeNode',
    props: {
        node: Object,
        level: {
            type: Number,
            default: 0
        },
        currentPath: String
    },
    data() {
        return {
            expanded: false,
            loading: false,
            children: [],
            childrenLoaded: false,
            childrenVisible: true // New property to control visibility of children
        };
    },
    computed: {
        nodeStyle() {
            return {
                paddingLeft: `${this.level * 20}px`
            };
        },
        isCurrentPath() {
            return this.node.path === this.currentPath;
        },
        isParentOfCurrentPath() {
            // Check if this node is a parent of the current path
            return this.currentPath.startsWith(this.node.path + '/');
        },
        shouldBeExpanded() {
            // Auto-expand if this is the current path, a parent of the current path,
            // or explicitly expanded by user
            return this.isCurrentPath || this.isParentOfCurrentPath || this.expanded;
        }
    },
    watch: {
        currentPath(newPath, oldPath) {
            // Check if we're navigating into this folder
            if (newPath === this.node.path) {
                this.expandNode();
            }

            // Check if we're navigating to a child path
            if (newPath.startsWith(this.node.path + '/')) {
                this.expandNode();
            }

            // Check if we've navigated out of this folder's hierarchy
            if (!newPath.startsWith(this.node.path) && oldPath.startsWith(this.node.path)) {
                // Only keep expanded if user manually expanded it before
                if (!this.expanded) {
                    this.childrenVisible = false;
                }
            }
        },
        shouldBeExpanded(newValue) {
            if (newValue && !this.childrenLoaded) {
                this.expandNode();
            }
        }
    },
    methods: {
        expandNode() {
            if (!this.childrenLoaded) {
                this.loading = true;
                this.$emit('node-click', this.node, (children) => {
                    this.children = children;
                    this.childrenLoaded = true;
                    this.loading = false;
                    this.expanded = true;
                    this.childrenVisible = true;
                });
            } else {
                this.expanded = true;
                this.childrenVisible = true;
            }
        },
        collapseNode() {
            this.expanded = false;
            this.childrenVisible = false;
        },
        toggle() {
            if (this.expanded) {
                this.collapseNode();
            } else {
                this.expandNode();
            }
        },
        handleFileClick(file) {
            this.$emit('file-click', file);
        },
        handleDownload(file) {
            this.$emit('download', file);
        },
        handleRightClick(event, item) {
            event.preventDefault();
            event.stopPropagation();
            if (item.type === 'dir') {
                this.$emit('node-right-click', event, item);
            } else {
                this.$emit('file-right-click', event, item);
            }
        }
    },
    mounted() {
        // Auto-expand if this is the current path or parent of current path
        if (this.isCurrentPath || this.isParentOfCurrentPath) {
            this.expandNode();
        }
    },
    template: `
        <div class="tree-node">
            <div
                :class="['tree-node-content', {'active': isCurrentPath, 'parent-of-active': isParentOfCurrentPath && !isCurrentPath}]"
                :style="nodeStyle"
                @click="toggle"
                @contextmenu="handleRightClick($event, node)">
                <div class="d-flex align-items-center w-100">
                    <div class="inline-flex items-center flex-grow-1" v-if="node.type === 'dir'">
                        <svg v-if="loading" xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-loader-2 animate-spin" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M12 3a9 9 0 1 0 9 9"></path>
                        </svg>
                        <svg v-else-if="expanded" xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-folder-open text-warning" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M5 4h4l3 3h7a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-11a2 2 0 0 1 2 -2"></path>
                            <path d="M6 10v8"></path>
                            <path d="M7 10v8"></path>
                            <path d="M8.5 10v8"></path>
                            <path d="M11 10v8"></path>
                            <path d="M14 10v8"></path>
                        </svg>
                        <svg v-else xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-folder text-warning" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M5 4h4l3 3h7a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-11a2 2 0 0 1 2 -2"></path>
                        </svg>
                        <span class="ml-1 tree-node-name">{{ node.name }}</span>
                    </div>
                    <div v-else class="inline-flex items-center gap-2 w-100 justify-content-between flex-grow-1">
                        <div class="d-flex align-items-center">
                            <component :is="'FileIcon'" :extension="node.extension" />
                            <span class="ml-1 tree-node-name">{{ node.name }}</span>
                        </div>
                        <div class="file-actions-inline text-end pull-right">
                            <button class="text-blue-500 btn-download-inline" @click.stop="handleDownload(node)" title="Download">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-download" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2"></path>
                                    <path d="M7 11l5 5l5 -5"></path>
                                    <path d="M12 4l0 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div v-if="expanded && childrenVisible && node.type === 'dir'" class="tree-node-children">
                <template v-if="children.length">
                    <folder-tree-node
                        v-for="child in children"
                        :key="child.path"
                        :node="child"
                        :level="level + 1"
                        :current-path="currentPath"
                        @node-click="(node, callback) => $emit('node-click', node, callback)"
                        @node-right-click="(event, node) => $emit('node-right-click', event, node)"
                        @file-click="(file) => $emit('file-click', file)"
                        @file-right-click="(event, file) => $emit('file-right-click', event, file)"
                        @download="(file) => $emit('download', file)" />
                </template>
                <div v-else-if="childrenLoaded" class="empty-folder-message" :style="nodeStyle">
                    <span class="text-muted">This folder is empty</span>
                </div>
            </div>
        </div>
    `
};

export default {
    props: ['page_data'],
    name: 'FileManager',
    components: {
        FileIcon,
        FolderTreeNode
    },
    data() {
        return {
            loading: false,
            filters: {
                sage_company_code: "",
                search: "",
            },
            companies: [],
            currentPath: '/',
            directories: [],
            files: [],
            isLoading: false,
            error: null,
            selectedFile: null,
            fileInfoModal: null,
            filePreviewModal: null,
            searchModal: null,
            connectionStatus: null,
            isTestingConnection: false,
            viewMode: 'treeview', // 'treeview' or 'grid'
            treeViewData: [],
            expandedPaths: new Set(),
            currentDisk: 'holding_smb', // 'file_server' or 'holding_smb'
            showDiskSelector: false,
            contextMenuVisible: false,
            contextMenuStyle: {
                top: '0px',
                left: '0px'
            },
            contextMenuTarget: null,
            previewingFile: null,
            filePreviewContent: null,
            searchQuery: '',
            searchResults: [],
            searchPerformed: false,
            isSearching: false,
            lastSelectedNode: null,
            showSearchResultsPanel: false,
            searchResultsSort: 'name',
            sortBy: 'name', // Default sorting by name
            sortDirection: 'asc', // Default sort direction

            // File upload state
            isDragging: false,
            isUploading: false,
            uploadQueue: [],
            uploadCancelTokens: {}
        }
    },
    computed: {
        pathSegments() {
            // Split path into segments for breadcrumb navigation
            if (this.currentPath === '/') {
                return [];
            }
            return this.currentPath.split('/').filter(segment => segment);
        },

        filteredTreeViewData() {
            const searchTerm = this.filters.search.toLowerCase().trim();
            let data = this.treeViewData;

            // First, filter by search term if present
            if (searchTerm) {
                data = data.filter(item => {
                    return item.name.toLowerCase().includes(searchTerm);
                });
            }

            // Then apply sorting
            return this.applySorting(data);
        },

        sortedDirectories() {
            return this.applySorting([...this.directories]);
        },

        sortedFiles() {
            return this.applySorting([...this.files]);
        },

        sortedSearchResults() {
            return this.applySorting([...this.searchResults], this.searchResultsSort);
        },

        connectionStatusClass() {
            if (!this.connectionStatus) return '';

            switch(this.connectionStatus.status) {
                case 'connected':
                    return 'alert-success';
                case 'failed':
                    return 'alert-danger';
                default:
                    return 'alert-warning';
            }
        },

        connectionStatusIcon() {
            if (!this.connectionStatus) return '';

            switch(this.connectionStatus.status) {
                case 'connected':
                    return 'fas fa-check-circle';
                case 'failed':
                    return 'fas fa-times-circle';
                default:
                    return 'fas fa-exclamation-circle';
            }
        },

        previewableExtensions() {
            return [
                // Images
                'jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg',
                // Documents
                'pdf',
                // Text
                'txt', 'log', 'md', 'json', 'xml', 'html', 'htm', 'js', 'css', 'csv'
            ];
        },

        allUploadsCompleted() {
            return this.uploadQueue.length > 0 && this.uploadQueue.every(file => file.status === 'completed');
        }
    },
    watch: {
        currentPath(newPath) {
            // When currentPath changes, update the tree view data
            this.updateTreeViewData();
        },
        'filters.search': _.debounce(function(newVal) {
            if (newVal && newVal.length > 2) {
                // Trigger deep search when search term is at least 3 characters
                this.performDeepSearch();
            } else if (!newVal) {
                // If search is cleared, reset to normal file display
                this.loadFiles();
            }
        }, 500)
    },
    async mounted() {
        // Test connection first
        // this.companyList();
        // await this.getCompanies();
        this.testConnection();
        console.log('File Manager mounted', this.companies);
        // Initialize modals
        this.fileInfoModal = new bootstrap.Modal(document.getElementById('fileInfoModal'));
        this.filePreviewModal = new bootstrap.Modal(document.getElementById('filePreviewModal'));
        this.searchModal = new bootstrap.Modal(document.getElementById('searchModal'));

        // Add global click event to hide context menu
        document.addEventListener('click', this.hideContextMenu);
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                this.hideContextMenu();
            }
        });

        // Focus the file manager container for keyboard navigation
        this.$el.focus();
    },
    beforeDestroy() {
        document.removeEventListener('click', this.hideContextMenu);
    },
    methods: {
        async companyList(){
            const endpoint = 'crm.company.list';
            const bitrixUserId = this.sharedState.bitrix_user_id ? this.sharedState.bitrix_user_id : this.page_data.user.bitrix_user_id;
            const bitrixWebhookToken = this.sharedState.bitrix_webhook_token ? this.sharedState.bitrix_webhook_token : this.page_data.user.bitrix_webhook_token;
            const requestData = {
                // id: this.obj.contact_id
            }
            this.callBitrixAPI(endpoint, bitrixUserId, bitrixWebhookToken, requestData)
            .then(response => {
                if (response.result) {
                   console.log('Contact:', response.result);
                    // this.filters.sage_company_code = response.result.UF_CRM_1681951981;
                }
            })
            .catch(error => {
                console.error('Error fetching contact:', error);
            });
        },
        async getCompanies(){
            const dateRange = JSON.parse(localStorage.getItem('dateRange'));
            const fromdate = DateTime.fromISO(dateRange[0]).toFormat('dd/MM/yyyy');
            const todate = DateTime.fromISO(dateRange[1]).toFormat('dd/MM/yyyy');
            const sageUrl = 'https://10.0.1.17/CrescoSage/api/V1/FOBank/2/Transactions';
            const user = "Felvin";
            const url = sageUrl + "?fromdate=" + fromdate  + "&todate=" + todate + "&currency=" + this.currency + "&user=" + user;

             await axios.post(url)
                .then(response => {
                    const companies = response.data.companies || [];
                    this.companies = _.sortBy(companies, 'companyName');
                    console.log('Companies:', this.companies);
                });
        },
        testConnection() {
            this.isTestingConnection = true;

            axios.get('/file-manager/test-connection')
                .then(response => {
                    // Updated to match the successResponse format from ApiResponser trait
                    if (response.data && response.data.status === 'success') {
                        this.connectionStatus = response.data.data;

                        if (this.connectionStatus.status === 'connected') {
                            // If connection is successful, load files
                            this.loadFiles();
                        }
                    } else {
                        throw new Error('Unexpected response format');
                    }
                })
                .catch(error => {
                    console.error('Error testing connection:', error);

                    this.connectionStatus = {
                        status: 'failed',
                        message: 'Connection to file server failed',
                        error: error.response?.data?.message || error.message
                    };
                })
                .finally(() => {
                    this.isTestingConnection = false;
                });
        },

        loadFiles() {
            if (this.connectionStatus && this.connectionStatus.status !== 'connected') {
                this.error = 'Cannot load files: Server connection is unavailable';
                return;
            }

            this.isLoading = true;
            this.error = null;

            axios.get('/file-manager/get-data', {
                params: {
                    action: 'list',
                    path: this.currentPath,
                    disk: this.currentDisk
                }
            })
            .then(response => {
                // Updated to match the successResponse format from ApiResponser trait
                if (response.data && response.data.status === 'success') {
                    this.directories = response.data.data.directories;
                    this.files = response.data.data.files;

                    // Update tree view data
                    this.updateTreeViewData();
                } else {
                    throw new Error('Unexpected response format');
                }
            })
            .catch(error => {
                console.error('Error loading files:', error);
                this.error = error.response?.data?.message || 'Failed to load files from server';
            })
            .finally(() => {
                this.isLoading = false;
            });
        },

        updateTreeViewData() {
            // Convert directories and files to tree view format
            const rootItems = [];

            // Add directories first
            this.directories.forEach(dir => {
                rootItems.push({
                    name: dir.name,
                    path: dir.path,
                    type: 'dir',
                    last_modified: dir.last_modified,
                });
            });

            // Add files
            this.files.forEach(file => {
                rootItems.push({
                    name: file.name,
                    path: file.path,
                    type: 'file',
                    extension: file.extension,
                    size: file.size,
                    last_modified: file.last_modified,
                });
            });

            this.treeViewData = rootItems;
        },

        handleTreeNodeClick(node, callback) {
            console.log('Node clicked:', node, callback);
            if (node.type === 'dir') {
                // Load directory contents
                axios.get('/file-manager/get-data', {
                    params: {
                        action: 'list',
                        path: node.path,
                        disk: this.currentDisk // Ensure we pass the current disk parameter
                    }
                })
                .then(response => {
                    if (response.data && response.data.status === 'success') {
                        const directories = response.data.data.directories;
                        const files = response.data.data.files;
                        const children = [];

                        // Add directories first
                        directories.forEach(dir => {
                            children.push({
                                name: dir.name,
                                path: dir.path,
                                type: 'dir',
                                last_modified: dir.last_modified,
                            });
                        });

                        // Add files
                        files.forEach(file => {
                            children.push({
                                name: file.name,
                                path: file.path,
                                type: 'file',
                                extension: file.extension,
                                size: file.size,
                                last_modified: file.last_modified,
                            });
                        });

                        // Call the callback with the children
                        if (typeof callback === 'function') {
                            callback(children);
                        }
                    } else {
                        throw new Error('Unexpected response format');
                    }
                })
                .catch(error => {
                    console.error('Error loading directory contents:', error);
                    if (typeof callback === 'function') {
                        callback([]);
                    }
                });
            }
        },

        refreshFiles() {
            this.loadFiles();
        },

        openDirectory(path) {
            this.currentPath = path;
            this.loadFiles();
        },

        navigateTo(path) {
            this.currentPath = path;
            this.loadFiles();
        },

        goToParentDirectory() {
            if (this.currentPath === '/') return;

            const pathParts = this.currentPath.split('/').filter(Boolean);
            pathParts.pop();

            if (pathParts.length === 0) {
                this.currentPath = '/';
            } else {
                this.currentPath = '/' + pathParts.join('/');
            }

            this.loadFiles();
        },

        getPathUpTo(index) {
            if (index < 0) return '/';
            return '/' + this.pathSegments.slice(0, index + 1).join('/');
        },

        fileInfo(file) {
            this.selectedFile = file;

            // Updated URL to match the correct route defined in web.php
            axios.get('/file-manager/get-data', {
                params: {
                    action: 'info',
                    path: this.currentPath,
                    filename: file.name
                }
            })
            .then(response => {
                // Updated to match the successResponse format from ApiResponser trait
                if (response.data && response.data.status === 'success') {
                    this.selectedFile = response.data.data;
                    this.fileInfoModal.show();
                } else {
                    throw new Error('Unexpected response format');
                }
            })
            .catch(error => {
                console.error('Error getting file info:', error);
                // Show error toast or notification
            });
        },

        downloadFile(file) {
            // Updated URL to match the correct route defined in web.php
            const downloadUrl = `/file-manager/get-data?action=download&path=${encodeURIComponent(this.currentPath)}&filename=${encodeURIComponent(file.name)}`;

            // Open in new tab to trigger download
            window.open(downloadUrl, '_blank');
        },

        getFileIconClass(extension) {
            // Define file type to icon mapping
            const iconMap = {
                'pdf': 'fas fa-file-pdf text-danger',
                'doc': 'fas fa-file-word text-primary',
                'docx': 'fas fa-file-word text-primary',
                'xls': 'fas fa-file-excel text-success',
                'xlsx': 'fas fa-file-excel text-success',
                'ppt': 'fas fa-file-powerpoint text-warning',
                'pptx': 'fas fa-file-powerpoint text-warning',
                'jpg': 'fas fa-file-image text-info',
                'jpeg': 'fas fa-file-image text-info',
                'png': 'fas fa-file-image text-info',
                'gif': 'fas fa-file-image text-info',
                'zip': 'fas fa-file-archive text-secondary',
                'rar': 'fas fa-file-archive text-secondary',
                'txt': 'fas fa-file-alt',
                'csv': 'fas fa-file-csv text-success',
                'json': 'fas fa-file-code text-primary',
                'html': 'fas fa-file-code text-danger',
                'js': 'fas fa-file-code text-warning',
                'css': 'fas fa-file-code text-info'
            };

            if (extension && iconMap[extension.toLowerCase()]) {
                return iconMap[extension.toLowerCase()];
            }

            // Default icon for unknown file types
            return 'fas fa-file text-secondary';
        },

        formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';

            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));

            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        },

        formatDate(timestamp) {
            if (!timestamp) return 'Unknown';

            const date = new Date(timestamp * 1000);
            return date.toLocaleString();
        },

        handleContextMenu(event, node) {
            event.preventDefault();
            this.contextMenuTarget = node;
            this.contextMenuVisible = true;
            this.lastSelectedNode = node;

            // Position the context menu
            this.contextMenuStyle = {
                top: `${event.clientY}px`,
                left: `${event.clientX}px`
            };
        },

        hideContextMenu() {
            this.contextMenuVisible = false;
        },

        openContextMenuTarget() {
            if (this.contextMenuTarget && this.contextMenuTarget.type === 'dir') {
                this.openDirectory(this.contextMenuTarget.path);
            }
            this.hideContextMenu();
        },

        toggleDiskSelector() {
                       this.showDiskSelector = !this.showDiskSelector;
        },

        switchDisk(diskName) {
            this.currentDisk = diskName;
            this.currentPath = '/';
            this.loadFiles();
            this.showDiskSelector = false;
        },

        canPreviewFile(file) {
            if (!file || file.type !== 'file') return false;
            return this.previewableExtensions.includes(file.extension.toLowerCase());
        },

        isImageFile(file) {
            if (!file) return false;
            const imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg'];
            return imageExtensions.includes(file.extension.toLowerCase());
        },

        isTextFile(file) {
            if (!file) return false;
            const textExtensions = ['txt', 'log', 'md', 'json', 'xml', 'html', 'htm', 'js', 'css', 'csv'];
            return textExtensions.includes(file.extension.toLowerCase());
        },

        previewFile(file) {
            if (!this.canPreviewFile(file)) return;

            this.previewingFile = file;
            this.filePreviewContent = null;

            if (this.isTextFile(file)) {
                // Load text content for text files

                axios.get('/file-manager/get-data', {
                    params: {
                        action: 'download',
                        path: this.getDirectoryPath(file.path),
                        filename: file.name,
                                               disk: this.currentDisk
                    },
                    responseType: 'text'
                })
                .then(response => {
                    this.filePreviewContent = response.data;
                })
                .catch(error => {
                    console.error('Error previewing file:', error);
                    this.filePreviewContent = 'Error loading file content.';
                });
            }

            this.hideContextMenu();
            this.filePreviewModal.show();
        },

        getDirectoryPath(filePath) {
            const parts = filePath.split('/');
            parts.pop();
            return parts.join('/') || '/';
        },

        getPreviewUrl(file) {
            if (!file) return '';
            return `/file-manager/get-data?action=download&path=${encodeURIComponent(this.getDirectoryPath(file.path))}&filename=${encodeURIComponent(file.name)}&disk=${this.currentDisk}`;
        },

        showSearchModal() {
            this.searchQuery = '';
            this.searchResults = [];
            this.searchPerformed = false;
            this.searchModal.show();
        },

        performSearch() {
            if (!this.searchQuery.trim()) return;

            this.isSearching = true;
            this.searchPerformed = true;

            axios.get('/file-manager/search', {
                params: {
                    query: this.searchQuery,
                    disk: this.currentDisk
                }
            })
            .then(response => {
                if (response.data && response.data.status === 'success') {
                    this.searchResults = response.data.data;
                } else {
                    this.searchResults = [];
                }
            })
            .catch(error => {
                console.error('Error performing search:', error);
                this.searchResults = [];
            })
            .finally(() => {
                this.isSearching = false;
            });
        },

        performDeepSearch() {
            const searchTerm = this.filters.search.toLowerCase().trim();

            if (!searchTerm) {
                // If search term is empty, just show current directory contents
                this.loadFiles();
                return;
            }

            this.isLoading = true;

            // Call the backend deep search endpoint
            axios.get('/file-manager/deep-search', {
                params: {
                    query: searchTerm,
                    path: this.currentPath,
                    disk: this.currentDisk
                }
            })
            .then(response => {
                if (response.data && response.data.status === 'success') {
                    this.searchResults = response.data.data;

                    // If we have search results, show them in a modal
                    if (this.searchResults.length > 0) {
                        this.searchPerformed = true;
                        this.showSearchResultsPanel = true;
                        // this.searchModal.show();
                    } else {
                        // Show notification that no results were found
                        console.log('No search results found for:', searchTerm);
                    }
                }
            })
            .catch(error => {
                console.error('Error performing deep search:', error);
            })
            .finally(() => {
                this.isLoading = false;
            });
        },

        navigateToSearchResult(result) {
            if (result.type === 'dir') {
                this.openDirectory(result.path);
            } else {
                const dirPath = this.getDirectoryPath(result.path);
                this.currentPath = dirPath;
                this.loadFiles();
                // After loading files, select the file
                setTimeout(() => {
                    this.fileInfo(result);
                }, 500);
            }

            // this.searchModal.hide();
        },

        clearSearchResults() {
            this.searchResults = [];
            this.showSearchResultsPanel = false;
            this.filters.search = '';
            this.loadFiles();
        },

        formatPathDisplay(path) {
            if (path === '/') {
                return this.currentDisk === 'file_server' ? 'SFTP Root' : 'Network Share Root';
            }

            // Truncate long paths for display
            let displayPath = path;
            if (path.length > 50) {
                const parts = path.split('/').filter(Boolean);
                if (parts.length > 3) {
                    displayPath = '/' + parts.slice(0, 1).join('/') + '/.../' + parts.slice(-2).join('/');
                }
            }

            return displayPath;
        },

        formatPathForDisplay(path) {
            // If it's the root path, show a special label
            if (path === '/') {
                return this.currentDisk === 'file_server' ? 'SFTP Root' : 'Network Share Root';
                                  }

            // Remove trailing slash if present
            path = path.replace(/\/$/, '');

            // Get directory parts
            const parts = path.split('/').filter(p => p.length > 0);

            // For paths with more than 3 segments, show first and last parts
            if (parts.length > 3) {
                return '/' + parts[0] + '/.../' + parts.slice(-2).join('/');
            }

            return path;
        },

        highlightMatch(text, query) {
            if (!query || !text) return text;

            query = query.toLowerCase();
            const index = text.toLowerCase().indexOf(query);

            if (index === -1) return text;

            const before = text.substring(0, index);
            const match = text.substring(index, index + query.length);
            const after = text.substring(index + query.length);

            return before + '<span class="match-highlight">' + match + '</span>' + after;
        },

        handleKeyDown(event) {
            // Only handle keyboard events when no modals are open
            if (document.querySelector('.modal.show')) return;

            switch(event.key) {
                case 'ArrowUp':
                    if (this.lastSelectedNode) {
                        // Logic to navigate up in the tree
                        event.preventDefault();
                    }
                    break;

                case 'ArrowDown':
                    if (this.lastSelectedNode) {
                        // Logic to navigate down in the tree
                        event.preventDefault();
                    }
                    break;

                case 'ArrowRight':
                    if (this.lastSelectedNode && this.lastSelectedNode.type === 'dir') {
                        // Expand folder
                        event.preventDefault();
                    }
                    break;

                case 'ArrowLeft':
                    if (this.lastSelectedNode && this.lastSelectedNode.type === 'dir') {
                        // Collapse folder
                        event.preventDefault();
                    }
                    break;

                case 'Enter':
                    if (this.lastSelectedNode) {
                        if (this.lastSelectedNode.type === 'dir') {
                            this.openDirectory(this.lastSelectedNode.path);
                        } else {
                                                       this.fileInfo(this.lastSelectedNode);
                        }

                        event.preventDefault();
                    }
                    break;

                case 'Backspace':
                    if (this.currentPath !== '/') {
                        this.goToParentDirectory();
                        event.preventDefault();
                    }
                    break;

                case 'F5':
                    this.refreshFiles();
                    event.preventDefault();
                    break;

                case '/':
                    if (event.ctrlKey || event.metaKey) {
                        this.showSearchModal();
                        event.preventDefault();
                    }
                    break;
            }
        },

        applySorting(items, sortProperty = null) {
            // Use the component's sortBy property if no specific sort property is provided
            const sortKey = sortProperty || this.sortBy;

            // Create a copy of the array to avoid mutating props or state directly
            const sortedItems = [...items];

            // First separate directories and files
            const directories = sortedItems.filter(item => item.type === 'dir');
            const files = sortedItems.filter(item => item.type === 'file');

            // Sort function that handles different types of values
            const sortFunction = (a, b) => {
                // Always put directories first (regardless of sort direction)
                if (a.type === 'dir' && b.type === 'file') return -1;
                if (a.type === 'file' && b.type === 'dir') return 1;

                let valA, valB;

                switch (sortKey) {
                    case 'name':
                        valA = a.name.toLowerCase();
                        valB = b.name.toLowerCase();
                        break;
                    case 'size':
                        // Files have size, directories don't
                        if (a.type === 'dir' && b.type === 'dir') {
                            valA = a.name.toLowerCase();
                            valB = b.name.toLowerCase();
                        } else {
                            valA = a.size || 0;
                            valB = b.size || 0;
                        }
                        break;
                    case 'last_modified':
                        valA = a.last_modified || 0;
                        valB = b.last_modified || 0;
                        break;
                    case 'type':
                        if (a.type === 'file' && b.type === 'file') {
                            valA = a.extension?.toLowerCase() || '';
                            valB = b.extension?.toLowerCase() || '';
                        } else {
                            // If comparing directories or mixed, fall back to name
                            valA = a.name.toLowerCase();
                            valB = b.name.toLowerCase();
                        }
                        break;
                    case 'path':
                        valA = a.path.toLowerCase();
                        valB = b.path.toLowerCase();
                        break;
                    default:
                        valA = a.name.toLowerCase();
                        valB = b.name.toLowerCase();
                }

                // Apply sort direction
                const direction = this.sortDirection === 'asc' ? 1 : -1;

                // Compare values
                if (valA < valB) return -1 * direction;
                if (valA > valB) return 1 * direction;
                return 0;
            };

            // Sort directories and files separately
            const sortedDirectories = directories.sort(sortFunction);
            const sortedFiles = files.sort(sortFunction);

            // Return combined array with directories first, then files
            return [...sortedDirectories, ...sortedFiles];
        },

        toggleSortDirection() {
            this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
        },

        // File upload methods
        handleDragOver(e) {
            this.isDragging = true;
            e.dataTransfer.dropEffect = 'copy';
        },

        handleDragLeave() {
            this.isDragging = false;
        },

        handleFileDrop(e) {
            this.isDragging = false;
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                this.uploadFiles(files);
            }
        },

        triggerFileUpload() {
            this.$refs.fileInput.click();
        },

        handleFileSelect(e) {
            const files = e.target.files;
            if (files.length > 0) {
                this.uploadFiles(files);
                // Reset the file input so the same file can be selected again
                e.target.value = null;
            }
        },

        uploadFiles(files) {
            if (!files || files.length === 0) return;

            this.isUploading = true;

            // Prepare files for upload queue
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                this.uploadQueue.push({
                    file: file,
                    name: file.name,
                    size: file.size,
                    progress: 0,
                    status: 'pending',
                    error: null
                });
            }

            // Process each file in the queue
            this.processUploadQueue();
        },

        processUploadQueue() {
            // Find next pending file
            const pendingIndex = this.uploadQueue.findIndex(item => item.status === 'pending');

            if (pendingIndex === -1) {
                return; // No pending uploads
            }

            const queueItem = this.uploadQueue[pendingIndex];
            queueItem.status = 'uploading';

            // Create form data
            const formData = new FormData();
            formData.append('file', queueItem.file);
            formData.append('path', this.currentPath);
            formData.append('disk', this.currentDisk);

            // Create cancel token
            const CancelToken = axios.CancelToken;
            const source = CancelToken.source();
            this.uploadCancelTokens[pendingIndex] = source;

            // Upload the file
            axios.post('/file-manager/upload', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                },
                onUploadProgress: (progressEvent) => {
                    const percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total);
                    queueItem.progress = percentCompleted;
                },
                cancelToken: source.token
            })
            .then(response => {
                if (response.data.status === 'success') {
                    queueItem.status = 'completed';
                    queueItem.progress = 100;

                    // Add the newly uploaded file to the file list
                    if (response.data.data) {
                        const uploadedFile = response.data.data;

                        // Add to tree and list view data if we're in the current directory
                        if (this.currentPath === uploadedFile.path.replace('/' + uploadedFile.name, '')) {
                            const fileData = {
                                name: uploadedFile.name,
                                path: uploadedFile.path,
                                size: uploadedFile.size,
                                last_modified: uploadedFile.last_modified,
                                extension: uploadedFile.extension,
                                type: 'file'
                            };

                            // Add to gridview or list data depending on current view
                            if (this.filesData) {
                                this.filesData.push(fileData);
                            }

                            // Add to treeview data
                            this.addToTreeViewData(fileData);

                            // Sort files after adding new one
                            this.sortFiles();
                        }
                    }

                    // Process next file in queue
                    this.processUploadQueue();
                } else {
                    queueItem.status = 'error';
                    queueItem.error = response.data.message || 'Upload failed';
                }
            })
            .catch(error => {
                if (axios.isCancel(error)) {
                    queueItem.status = 'cancelled';
                    console.log('Upload cancelled');
                } else {
                    queueItem.status = 'error';
                    queueItem.error = error.response?.data?.message || 'Upload failed';
                    console.error('Upload error:', error);
                }

                // Process next file anyway
                this.processUploadQueue();
            });
        },

        // Cancel a specific upload
        cancelUpload(index) {
            const queueItem = this.uploadQueue[index];
            if (queueItem && queueItem.status === 'uploading') {
                if (this.uploadCancelTokens[index]) {
                    this.uploadCancelTokens[index].cancel('Upload cancelled by user');
                    delete this.uploadCancelTokens[index];
                }
                queueItem.status = 'cancelled';

                // Process next file
                this.processUploadQueue();
            }
        },

        // Cancel all uploads
        cancelAllUploads() {
            for (let i = 0; i < this.uploadQueue.length; i++) {
                if (this.uploadQueue[i].status === 'uploading' || this.uploadQueue[i].status === 'pending') {
                    if (this.uploadCancelTokens[i]) {
                        this.uploadCancelTokens[i].cancel('Upload cancelled by user');
                        delete this.uploadCancelTokens[i];
                    }
                    this.uploadQueue[i].status = 'cancelled';
                }
            }
        },

        // Close upload progress panel
        closeUploadProgress() {
            this.isUploading = false;
            this.uploadQueue = [];
            this.uploadCancelTokens = {};
        },

        // Helper to format file size
        formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';

            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));

            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        },

        // Add uploaded file to treeview data structure
        addToTreeViewData(fileData) {
            // Find the node that represents the current directory
            const pathParts = this.currentPath.split('/').filter(Boolean);
            let currentLevel = this.treeViewData;

            // Navigate to the correct location in the tree
            for (const part of pathParts) {
                const foundNode = currentLevel.find(node => node.name === part && node.type === 'directory');
                if (foundNode && foundNode.children) {
                    currentLevel = foundNode.children;
                } else {
                    console.error('Could not find location in tree to add file');
                    return;
                }
            }

            // Add the file to this level's children
            currentLevel.push({
                ...fileData,
                children: []
            });
        },

        // Sort files after adding new ones
        sortFiles() {
            if (this.filesData) {
                // Sort directories first, then files
                this.filesData.sort((a, b) => {
                    if (a.type === 'directory' && b.type !== 'directory') return -1;
                    if (a.type !== 'directory' && b.type === 'directory') return 1;
                    return a.name.localeCompare(b.name);
                });
            }
        }
    }
}
</script>

<style scoped>
.file-manager-container {
    /* background: #fff; */
    /* border-radius: 16px; */
    box-shadow: 0 8px 24px rgba(0,0,0,0.06);
    padding: 28px;
    height: 100%;
    display: flex;
    flex-direction: column;
}

/* Header styling */
.file-manager-container h3 {
    font-weight: 600;
    font-size: 1.5rem;
    color: #1e293b;
    margin-bottom: 1.5rem;
}

.file-manager-container h3 svg {
    color: #4361ee;
}

/* Navigation bar with improved styling */
.mb-4.card {
    border: none;
    /* background-color: #f8fafc; */
    box-shadow: 0 2px 8px rgba(0,0,0,0.03);
    /* border-radius: 12px; */
}

.mb-4.card .card-body {
    padding: 14px 18px;
}

.btn-group .btn {
    border-radius: 8px;
    margin-right: 8px;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    transition: all 0.25s ease;
    padding: 0.5rem 0.85rem;
}

.btn-group .btn svg {
    margin-right: 6px;
}

.btn-group .btn-primary {
    background-color: #4f46e5;
    border-color: #4f46e5;
    box-shadow: 0 2px 5px rgba(79, 70, 229, 0.2);
}

.btn-group .btn-primary:hover {
    background-color: #4338ca;
    border-color: #4338ca;
    box-shadow: 0 4px 8px rgba(79, 70, 229, 0.3);
    transform: translateY(-1px);
}

.btn-group .btn-outline-secondary:hover {
    background-color: #f1f5f9;
    transform: translateY(-1px);
}

.btn-group .btn-light {
    background-color: #fff;
    border-color: #e2e8f0;
}

.btn-group .btn-light:hover {
    background-color: #f8fafc;
    border-color: #cbd5e1;
    transform: translateY(-1px);
}

/* Tree view with modern styling */
.tree-node {
    width: 100%;
}

.tree-node-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 12px;
    cursor: pointer;
    margin-bottom: 1px;
    user-select: none;
    transition: all 0.2s ease;
    border-bottom: 1px solid #f1f5f9;
}

.tree-node-content:hover {
    background-color: rgba(79, 70, 229, 0.05);
    cursor: pointer;
}

.tree-node-content.active {
    font-weight: 600;
    color: #4f46e5;
}

.tree-node-content.parent-of-active {
    background-color: rgba(79, 70, 229, 0.02);
    border-left: 2px solid rgba(79, 70, 229, 0.3);
}

.tree-node-name {
    font-size: 0.95rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    margin-left: 4px;
    color: #334155;
}

/* Improve hover states for directories */
.tree-node-content:hover {
    background-color: rgba(79, 70, 229, 0.05);
    cursor: pointer;
}

/* Position download icon to the right */
.tree-node-actions {
    margin-left: auto;
    display: flex;
    align-items: center;
    justify-content: flex-end;
    min-width: 40px;
}

.btn-icon.btn-outline-primary {
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Improve file card download button positioning */
.file-card .file-actions {
    margin-left: auto;
    display: flex;
    align-items: center;
    justify-content: flex-end;
}

/* Add cursor:pointer to directory elements */
.tree-node-content {
    /* ...existing code... */
    cursor: pointer;
}

/* Shadow transitions */
.card, .btn, .modal-content, .context-menu {
    transition: box-shadow 0.3s ease, transform 0.3s ease;
}

/* Improved buttons */
.btn-primary {
    box-shadow: 0 2px 5px rgba(79, 70, 229, 0.2);
}

.btn-primary:hover {
    box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
    transform: translateY(-1px);
}

.btn-outline-primary {
    border-color: #4f46e5;
    color: #4f46e5;
}

.btn-outline-primary:hover {
    background-color: rgba(79, 70, 229, 0.05);
    border-color: #4f46e5;
    color: #4338ca;
    box-shadow: 0 2px 5px rgba(79, 70, 229, 0.1);
    transform: translateY(-1px);
}

/* Beautiful empty states */
.empty-folder-state {
    background-color: #f8fafc;
    border-radius: 16px;
    padding: 32px 20px;
}

.empty-folder-state svg {
    opacity: 0.7;
    margin-bottom: 16px;
    color: #64748b;
}

.empty-folder-state .text-muted {
    font-size: 1.05rem;
    font-weight: 500;
    color: #64748b;
}

/* Breadcrumb enhancements */
.file-manager-breadcrumb .breadcrumb {
    background: transparent;
    padding: 0;
    margin-bottom: 0;
    font-size: 0.92rem;
}

.file-manager-breadcrumb .breadcrumb-item + .breadcrumb-item::before {
    content: "/";
    color: #94a3b8;
}

.file-manager-breadcrumb .breadcrumb-item a {
    color: #4f46e5;
    text-decoration: none;
}

.file-manager-breadcrumb .breadcrumb-item a:hover {
    text-decoration: underline;
}

.file-manager-breadcrumb .breadcrumb-item.active {
    color: #334155;
    font-weight: 500;
}

/* Context Menu with enhanced styling */
.context-menu {
    position: fixed;
    z-index: 1000;
    min-width: 240px;
    box-shadow: 0 6px 24px rgba(0, 0, 0, 0.12);
    background-color: #fff;
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid #e2e8f0;
    animation: fadeIn 0.2s ease-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: rotate(-15deg) scale(0.8); }
    to { opacity: 1; transform: rotate(0) scale(1); }
}

@keyframes slideIn {
    from { opacity: 0; transform: translateX(-10px); }
    to { opacity: 1; transform: translateX(0); }
}

.context-menu .list-group {
    border-radius: 12px;
}

.context-menu .list-group-item {
    padding: 12px 18px;
    cursor: pointer;
    border: none;
    font-weight: 500;
    transition: all 0.2s ease;
    color: #334155;
}

.context-menu .list-group-item:hover {
    background-color: rgba(79, 70, 229, 0.08);
    color: #4f46e5;
}

.context-menu .list-group-item svg {
    margin-right: 12px;
    width: 18px;
    height: 18px;
}

/* Improved Disk Selector design */
.disk-selector-popover {
    position: absolute;
    z-index: 900;
    width: 280px;
    margin-top: -4px;
    margin-left: 20px;
    animation: fadeIn 0.25s ease-out;
}

.disk-selector-popover .card {
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.08);
    overflow: hidden;
}

.disk-selector-popover .list-group-item {
    padding: 14px 18px;
    border-left: none;
    border-right: none;
    transition: all 0.2s ease;
    border-color: #f1f5f9;
}

.disk-selector-popover .list-group-item:first-child {
    border-top: none;
}

.disk-selector-popover .list-group-item:last-child {
    border-bottom: none;
}

.disk-selector-popover .list-group-item.active {
    background-color: #4f46e5;
    border-color: #4f46e5;
    z-index: auto;
    font-weight: 500;
}

.disk-selector-popover .list-group-item:hover:not(.active) {
    background-color: #f8fafc;
}

/* Elegant modals */
.modal-content {
    border-radius: 16px;
    border: none;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.12);
    overflow: hidden;
}

.modal-header {
    border-bottom: 1px solid #f1f5f9;
    padding: 18px 24px;
    background-color: #fff;
}

.modal-body {
    padding: 24px;
}

.modal-footer {
    border-top: 1px solid #f1f5f9;
    padding: 16px 24px;
    background-color: #fafafa;
}

.modal-title {
    font-weight: 600;
    color: #1e293b;
}

/* File preview enhancements */
.text-preview {
    max-height: 70vh;
    overflow: auto;
    background-color: #f8fafc;
    border-radius: 10px;
    padding: 20px;
    border: 1px solid #e2e8f0;
    box-shadow: inset 0 2px 4px rgba(0,0,0,0.04);
}

.text-preview pre {
    margin-bottom: 0;
    white-space: pre-wrap;
    font-size: 0.92rem;
    font-family: "SF Mono", "Cascadia Code", "Menlo", "Consolas", monospace;
    color: #334155;
}

/* PDF preview container */
.pdf-container {
    height: 75vh;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    border: 1px solid #e2e8f0;
}

.pdf-container iframe {
    border: none;
}

/* Search results with elegant styling */
.search-results {
    max-height: 400px;
    overflow-y: auto;
    margin-top: 16px;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
}

.search-results h6 {
    padding: 14px 18px;
    margin: 0;
    background-color: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    font-weight: 600;
    color: #334155;
}

.search-results .list-group-item {
    padding: 14px 18px;
    border-left: none;
    border-right: none;
    transition: all 0.2s ease;
    border-color: #f1f5f9;
    animation: slideIn 0.3s ease-out;
    animation-fill-mode: both;
}

.search-results .list-group-item:nth-child(2) { animation-delay: 0.05s; }
.search-results .list-group-item:nth-child(3) { animation-delay: 0.1s; }
.search-results .list-group-item:nth-child(4) { animation-delay: 0.15s; }
.search-results .list-group-item:nth-child(5) { animation-delay: 0.2s; }

.search-results .list-group-item:hover {
    background-color: rgba(79, 70, 229, 0.05);
}

.search-results .list-group-item small {
    font-size: 0.82rem;
    color: #64748b;
    display: block;
    margin-top: 5px;
}

.search-result-item {
    padding: 12px;
    border-radius: 8px;
    border-bottom: 1px solid #f1f5f9;
    margin-bottom: 8px;
    transition: all 0.2s ease;
    cursor: pointer;
}

.search-result-item:hover {
    background-color: rgba(79, 70, 229, 0.03);
    transform: translateX(2px);
}

.search-result-item .file-name {
    font-weight: 500;
    color: #111827;
    margin-bottom: 4px;
    max-width: 100%;
}

.search-result-item .file-path {
    color: #64748b;
    font-size: 0.85rem;
}

.search-result-item .file-meta {
    font-size: 0.8rem;
    color: #94a3b8;
}

.search-result-item .match-highlight {
    background-color: rgba(250, 204, 21, 0.2);
    padding: 1px 2px;
    border-radius: 3px;
    font-weight: 700;
    color: #854d0e;
}

/* Enhance the dynamic treeview navigation */
.tree-node-content.parent-of-active {
    background-color: rgba(79, 70, 229, 0.02);
    border-left: 2px solid rgba(79, 70, 229, 0.3);
}

.tree-node-children {
    animation: fadeInContent 0.3s ease-out;
    transform-origin: top;
}

/* Empty folder message styling */
.empty-folder-message {
    padding: 8px 16px;
    margin-top: 4px;
    margin-bottom: 4px;
    font-style: italic;
    border-radius: 6px;
    background-color: #f8fafc;
    font-size: 0.85rem;
}

@keyframes fadeInContent {
    from { opacity: 0; transform: scaleY(0.8); }
    to { opacity: 1; transform: scaleY(1); }
}

/* Tree node hierarchy line indicators */
.tree-node-children {
    position: relative;
    margin-left: 8px;
    padding-left: 12px;
    border-left: 1px dashed #e2e8f0;
}

/* Improve the focus state for keyboard navigation */
.tree-node-content:focus {
    outline: 2px solid rgba(79, 70, 229, 0.5);
    outline-offset: -2px;
    background-color: rgba(79, 70, 229, 0.05);
}

/* Style the inline download button */
.file-actions-inline {
    opacity: 0;
    transition: opacity 0.2s ease-out;
}

.tree-node-content:hover .file-actions-inline {
    opacity: 1;
}

.btn-download-inline {
    background: transparent;
    border: none;
    color: #4f46e5;
    padding: 3px 6px;
    border-radius: 4px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
}

.btn-download-inline:hover {
    background-color: rgba(79, 70, 229, 0.1);
    color: #4338ca;
    transform: translateY(-1px);
}

.btn-download-inline svg {
    width: 16px;
    height: 16px;
}

/* Make the tree node content display flex to position elements properly */
.tree-node-content .d-flex {
    width: 100%;
    justify-content: space-between;
}

/* Ensure the filename takes available space but doesn't overflow */
.tree-node-content .tree-node-name {
    flex-grow: 1;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    padding-right: 16px;
}

/* File upload styles */
.file-manager-treeview-container {
    position: relative;
    width: 100%;
    height: 100%;
}

.file-drop-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(79, 70, 229, 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
    border-radius: 12px;
    padding: 20px;
    transition: background-color 0.3s ease;
}

.file-drop-message {
    text-align: center;
    color: #334155;
}

.file-drop-message h3 {
    font-size: 1.25rem;
    margin: 0.5rem 0;
}

.file-drop-message p {
    font-size: 0.9rem;
    color: #64748b;
}

/* Upload progress overlay */
.upload-progress-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(255, 255, 255, 0.9);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
    border-radius: 12px;
    padding: 20px;
    transition: background-color 0.3s ease;
}

.upload-progress-container {
    width: 100%;
}

.upload-progress-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.upload-progress-header h4 {
    font-size: 1.1rem;
    margin: 0;
}

.upload-actions {
    margin-top: 1rem;
    text-align: right;
}

.upload-item {
    margin-bottom: 0.5rem;
}

.upload-item-info {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.2rem;
}

.upload-item-name {
    font-weight: 500;
    color: #111827;
}

.upload-item-size {
    font-size: 0.85rem;
    color: #64748b;
}

.upload-item-progress {
    display: flex;
    align-items: center;
}

.progress {
    flex-grow: 1;
    height: 8px;
    border-radius: 4px;
    background-color: #e2e8f0;
    margin-right: 0.5rem;
}

.progress-bar {
    height: 100%;
    border-radius: 4px;
}

.upload-completed {
    color: #4caf50;
    font-size: 0.9rem;
}

.upload-completed svg {
    width: 16px;
    height: 16px;
    vertical-align: middle;
    margin-left: 4px;
}

/* Hide the file input */
.hidden-file-input {
    display: none;
}
</style>
