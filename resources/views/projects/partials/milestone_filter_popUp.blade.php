@php
    $filtersPopupMode = $filtersPopupMode ?? 'standard';
@endphp

<style>
    .showAllMilestonesIcon {
        width: 27px;
        transition: filter 0.2s;
    }

    .showAllMilestonesIcon.enabled {
        filter: grayscale(0);
    }

    .showAllMilestonesIcon.disabled {
        filter: grayscale(1);
    }

    .showCompletedProjectGroup {
        display: flex;
        gap: 17px;
    }

    .showCompletedProjects {
        width: 27px;
        height: 27px;
        margin-top: 0;
    }

    .showCompletedProjects:hover {
        cursor: pointer;
    }

    .showCompletedProjectsUnabled {
        filter: grayscale(1);
    }

    .hideUnasignedTasks {
        margin-top: 0;
        width: 27px;
    }

    .hideUnasignedTasks:hover {
        cursor: pointer;
    }

    .filterOption {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 12px 4px;
        border-bottom: 1px solid #e9ecef;
    }

    .filterBinaryGroup {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 4px;
        width: 100%;
    }

    .filterBinaryOption {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 12px;
        font-weight: 500;
        color: #444;
        margin: 0;
        cursor: pointer;
        padding: 8px 10px;
        border-radius: 8px;
        width: 100%;
        transition: background-color 0.15s ease;
    }

    .filterBinaryOption:hover {
        background-color: #f5f5f5;
    }

    .filterBinaryOption input[type='radio'] {
        margin: 0;
        accent-color: #aa182c;
        cursor: pointer;
    }

    .toggleFilterContentInner {
        padding: 8px 0 4px;
    }

    .filterOption:last-child {
        border-bottom: none;
    }

    .filterLabel {
        font-size: 14px;
        font-weight: 600;
        color: #2d3436;
        margin: 0;
    }

    .priorityFilterBlock {
        margin-top: 4px;
        border-bottom: 1px solid #e9ecef;
    }

    .priorityFilterHeader {
        display: flex;
        align-items: center;
        justify-content: space-between;
        cursor: pointer;
        user-select: none;
        padding: 12px 6px 12px 4px;
    }

    .priorityFilterChevron {
        font-size: 1rem;
        color: #6c757d;
        width: 1em;
        height: 1em;
        display: inline-block;
        line-height: 1;
        transform: rotate(0deg);
        transform-origin: center;
        transition: transform 0.25s ease-in-out;
        will-change: transform;
    }

    .priorityFilterBlock.open .priorityFilterChevron {
        transform: rotate(180deg);
    }

    .priorityFilterContent {
        display: none;
        margin: 6px 0 10px;
        padding: 10px 10px 8px;
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 10px;
    }

    .priorityFilterBlock.open .priorityFilterContent {
        display: block;
    }

    .priorityCheckboxList {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .priorityCheckboxItem {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 13px;
        font-weight: 500;
        color: #2d3436;
        padding: 6px 8px;
        border-radius: 8px;
        transition: background-color 0.2s ease;
        cursor: pointer;
    }

    .priorityCheckboxItem:hover {
        background-color: #ffffff;
    }

    .priorityCheckboxItem input {
        margin: 0;
        width: 16px;
        height: 16px;
        accent-color: #aa182c;
        cursor: pointer;
    }

    .priorityCheckboxItem input:focus-visible {
        outline: 2px solid rgba(170, 24, 44, 0.25);
        outline-offset: 1px;
    }

    .filtersResetBtn {
        font-size: 12px;
        padding: 4px 10px;
        line-height: 1;
    }

    .filtersResetContainer {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        padding-top: 10px;
    }

    .dateRangeFilterGroup {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .dateRangeFilterLabel {
        font-size: 12px;
        font-weight: 600;
        color: #495057;
        margin: 0 0 4px;
    }

    .dateRangeFilterRow {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
    }

    .projectFilterBlock {
        border-bottom: 1px solid #e9ecef;
    }

    .projectFilterHeader {
        display: flex;
        align-items: center;
        justify-content: space-between;
        cursor: pointer;
        user-select: none;
        padding: 12px 6px 12px 4px;
    }

    .projectFilterChevron {
        font-size: 1rem;
        color: #6c757d;
        width: 1em;
        height: 1em;
        display: inline-block;
        line-height: 1;
        transform: rotate(0deg);
        transform-origin: center;
        transition: transform 0.25s ease-in-out;
        will-change: transform;
    }

    .projectFilterBlock.open .projectFilterChevron {
        transform: rotate(180deg);
    }

    .projectFilterContent {
        display: none;
        margin-top: 8px;
    }

    .projectFilterBlock.open .projectFilterContent {
        display: block;
    }

    .projectFilterInputWrap {
        display: flex;
        gap: 8px;
        margin-top: 8px;
        align-items: center;
    }

    .projectFilterInputWrap input {
        flex: 1;
    }

    .projectFilterAutocomplete {
        position: relative;
        flex: 1;
        width: 100%;
    }

    .projectSuggestions {
        position: absolute;
        top: calc(100% + 4px);
        left: 0;
        right: 0;
        max-height: 200px;
        overflow-y: auto;
        background: #ffffff;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        z-index: 1056;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        display: none;
    }

    .projectSuggestions.visible {
        display: block;
    }

    .projectSuggestionItem {
        padding: 8px 10px;
        font-size: 13px;
        cursor: pointer;
        border-bottom: 1px solid #f1f3f5;
    }

    .projectSuggestionItem:last-child {
        border-bottom: none;
    }

    .projectSuggestionItem:hover {
        background: #f8f9fa;
    }

    .projectSuggestionItem.active {
        background: #eef2ff;
    }

    .projectSuggestionEmpty {
        color: #6c757d;
        cursor: default;
        font-style: italic;
    }

    .projectSuggestionEmpty:hover,
    .projectSuggestionEmpty.active {
        background: #ffffff;
    }

    #applyProjectFilterBtn,
    #applyWorkspaceFilterBtn,
    #applyRequestedByFilterBtn,
    #applyAssignedToFilterBtn {
        width: 27px;
        height: 27px;
        min-width: 27px;
        min-height: 27px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
    }

    #applyProjectFilterBtn i,
    #applyWorkspaceFilterBtn i,
    #applyRequestedByFilterBtn i,
    #applyAssignedToFilterBtn i {
        font-size: 12px;
    }

    .selectedProjectList {
        padding-bottom: 10px;
        margin-top: 8px;
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }

    .selectedProjectTag {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #f1f3f5;
        border: 1px solid #dee2e6;
        border-radius: 20px;
        padding: 4px 10px;
        font-size: 12px;
        font-weight: 600;
        color: #2d3436;
    }

    .selectedProjectTag button {
        border: none;
        background: transparent;
        padding: 0;
        line-height: 1;
        cursor: pointer;
        color: #6c757d;
    }

    .activeFiltersBar {
        margin-top: 12px;
        padding: 10px;
        border: 1px solid #e9ecef;
        border-radius: 10px;
        background: #f8f9fa;
    }

    .activeFiltersTitle {
        font-size: 12px;
        font-weight: 700;
        color: #495057;
        margin: 0 0 8px;
    }

    .activeFiltersList {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }

    .activeFilterChip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #ffffff;
        border: 1px solid #dee2e6;
        border-radius: 20px;
        padding: 4px 10px;
        font-size: 12px;
        font-weight: 600;
        color: #2d3436;
    }

    .activeFilterChip button {
        border: none;
        background: transparent;
        padding: 0;
        line-height: 1;
        cursor: pointer;
        color: #6c757d;
        font-size: 13px;
    }

    #filtersModal .modal-dialog {
        max-width: 980px;
    }

    #filtersModal .modal-content {
        border: none;
        border-radius: 20px;
        box-shadow: 0 24px 80px rgba(0, 0, 0, 0.10), 0 2px 6px rgba(0, 0, 0, 0.03);
        overflow: hidden;
        animation: fmSlideIn 0.35s cubic-bezier(0.16, 1, 0.3, 1);
    }

    @keyframes fmSlideIn {
        from {
            opacity: 0;
            transform: translateY(10px) scale(0.98);
        }

        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    #filtersModal .modal-header {
        padding: 32px 36px 20px 36px;
        background: transparent;
        border-bottom: 1px solid #bfbfbf;
    }

    #filtersModal .modal-title {
        font-size: 20px;
        font-weight: 800;
        color: #111;
        letter-spacing: -0.5px;
    }

    #filtersModal .btn-close {
        opacity: 0.25;
        transition: opacity 0.2s ease, transform 0.2s ease;
    }

    #filtersModal .btn-close:hover {
        opacity: 0.7;
        transform: scale(1.1);
    }

    #filtersModal .modal-body {
        padding: 20px 36px 32px;
    }

    .filtersGrid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 0 28px;
    }

    .filtersGridCol {
        display: flex;
        flex-direction: column;
    }

    .filtersGridCol--toggles .filtersTogglesSection {
        margin-top: 0;
        padding-top: 0;
        border-top: none;
    }

    @media (max-width: 1199.98px) {
        #filtersModal .modal-dialog {
            max-width: 720px;
        }

        .filtersGrid {
            grid-template-columns: 1fr;
            gap: 0;
        }
    }

    #filtersModal .filterLabel {
        font-size: 10.5px;
        font-weight: 700;
        color: black;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin: 0;
        transition: color 0.2s ease;
    }

    #filtersModal .projectFilterHeader:hover .filterLabel,
    #filtersModal .priorityFilterHeader:hover .filterLabel {
        color: #555;
    }

    #filtersModal .projectFilterBlock,
    #filtersModal .priorityFilterBlock {
        border: 1px solid #c0c0c0;
        background: #fafafa;
        border-radius: 10px;
        margin-top: 0;
        margin-bottom: 10px;
        padding: 2px 15px;
    }

    #filtersModal .projectFilterHeader,
    #filtersModal .priorityFilterHeader {
        padding: 10px 0 10px;
    }

    #filtersModal .projectFilterChevron,
    #filtersModal .priorityFilterChevron {
        font-size: 1rem;
        color: #111111;
        transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1), color 0.2s ease;
    }

    #filtersModal .projectFilterHeader:hover .projectFilterChevron,
    #filtersModal .priorityFilterHeader:hover .priorityFilterChevron {
        color: #888;
    }

    #filtersModal .projectFilterBlock.open .projectFilterChevron,
    #filtersModal .priorityFilterBlock.open .priorityFilterChevron {
        transform: rotate(180deg);
    }

    #filtersModal .projectFilterContent {
        display: block;
        max-height: 0;
        opacity: 0;
        overflow: hidden;
        transition: max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.3s ease, margin 0.3s ease;
        margin-top: 0;
    }

    #filtersModal .projectFilterBlock.open .projectFilterContent {
        display: block;
        max-height: 300px;
        opacity: 1;
        overflow: visible;
        margin-top: 6px;
    }

    #filtersModal .priorityFilterContent {
        display: block;
        max-height: 0;
        opacity: 0;
        overflow: hidden;
        transition: max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.3s ease, margin 0.3s ease, padding 0.3s ease;
        margin: 0;
        padding: 0;
        background: transparent;
        border: none;
        border-radius: 0;
    }

    #filtersModal .priorityFilterBlock.open .priorityFilterContent {
        display: block;
        max-height: 500px;
        opacity: 1;
        overflow: visible;
        margin: 0px 0 8px;
        padding: 0px 0 4px;
    }

    #filtersModal .projectFilterInputWrap {
        display: flex;
        gap: 10px;
        align-items: center;
        margin-top: 0;
    }

    #filtersModal .form-control,
    #filtersModal .form-select {
        border: 1px solid #c8c5c5;
        background: #ffffff;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 500;
        padding: 10px 14px;
        color: #333;
        transition: background 0.25s ease, box-shadow 0.25s ease;
    }

    #filtersModal .form-control:focus,
    #filtersModal .form-select:focus {
        background: #fff;
        box-shadow: 0 0 0 2px rgba(0, 0, 0, 0.08);
        outline: none;
    }

    #filtersModal .form-control::placeholder {
        color: #bbb;
        font-weight: 400;
    }

    #filtersModal .projectSuggestions {
        border: none;
        border-radius: 12px;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.10);
        top: calc(100% + 6px);
    }

    #filtersModal .projectSuggestionItem {
        padding: 10px 14px;
        font-size: 13px;
        border-bottom: 1px solid #f5f5f5;
        transition: background 0.15s ease;
    }

    #filtersModal .projectSuggestionItem:hover,
    #filtersModal .projectSuggestionItem.active {
        background: #f8f8f8;
    }

    #filtersModal .projectSuggestionEmpty {
        color: #aaa;
    }

    #filtersModal .projectSuggestionEmpty:hover,
    #filtersModal .projectSuggestionEmpty.active {
        background: transparent;
    }

    #filtersModal #applyProjectFilterBtn,
    #filtersModal #applyWorkspaceFilterBtn,
    #filtersModal #applyRequestedByFilterBtn,
    #filtersModal #applyAssignedToFilterBtn {
        width: 32px;
        height: 32px;
        min-width: 32px;
        min-height: 32px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        border: none;
        background: #AA182C;
        color: #fff;
        transition: background 0.2s ease, transform 0.15s ease;
    }

    #filtersModal #applyProjectFilterBtn:hover,
    #filtersModal #applyWorkspaceFilterBtn:hover,
    #filtersModal #applyRequestedByFilterBtn:hover,
    #filtersModal #applyAssignedToFilterBtn:hover {
        background: #8f1525;
        transform: scale(1.05);
    }

    #filtersModal #applyProjectFilterBtn i,
    #filtersModal #applyWorkspaceFilterBtn i,
    #filtersModal #applyRequestedByFilterBtn i,
    #filtersModal #applyAssignedToFilterBtn i {
        font-size: 11px;
    }

    #filtersModal .selectedProjectList {
        padding-bottom: 4px;
        margin-top: 8px;
    }

    #filtersModal .selectedProjectTag {
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 20px;
        padding: 4px 12px;
        font-size: 11px;
        font-weight: 600;
        color: #666;
        transition: border-color 0.2s ease;
    }

    #filtersModal .selectedProjectTag:hover {
        border-color: #aaa;
    }

    #filtersModal .selectedProjectTag button {
        color: #ccc;
        transition: color 0.15s ease;
    }

    #filtersModal .selectedProjectTag button:hover {
        color: #aa182c;
    }

    #filtersModal .priorityCheckboxList {
        gap: 2px;
    }

    #filtersModal .priorityCheckboxItem {
        font-size: 13px;
        font-weight: 500;
        color: #444;
        padding: 8px 10px;
        border-radius: 8px;
        transition: background-color 0.15s ease;
    }

    #filtersModal .priorityCheckboxItem:hover {
        background-color: #f5f5f5;
    }

    #filtersModal .priorityCheckboxItem input {
        accent-color: #aa182c;
    }

    #filtersModal .priorityCheckboxItem input:focus-visible {
        outline: 2px solid rgba(0, 0, 0, 0.12);
        outline-offset: 1px;
    }

    #filtersModal .dateRangeFilterLabel {
        font-size: 10px;
        font-weight: 700;
        color: #aaa;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin: 0 0 6px;
    }

    #filtersModal .dateRangeFieldControl {
        display: flex;
        flex-direction: column;
    }

    #filtersModal .dateRangeFieldSelectWrap {
        position: relative;
    }

    #filtersModal #dateRangeField {
        cursor: pointer;
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        padding-right: 34px;
    }

    #filtersModal .dateRangeFieldSelectWrap .dateRangeFieldChevron {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 10px;
        color: #8a8a8a;
        pointer-events: none;
        transition: color 0.2s ease;
    }

    #filtersModal .dateRangeFieldSelectWrap:hover .dateRangeFieldChevron {
        color: #444;
    }

    #filtersModal .dateRangeFilterGroup {
        gap: 14px;
    }

    .filtersTogglesSection {
        margin-top: 24px;
        padding-top: 24px;
        border-top: 1px solid #f0f0f0;
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    #filtersModal .filterOption {
        padding: 10px 0;
        border-bottom: none;
    }

    #filtersModal .filterOption .filterLabel {
        font-size: 13px;
        font-weight: 600;
        color: black;
        text-transform: none;
        letter-spacing: 0;
    }

    #filtersModal .showCompletedProjects,
    #filtersModal .hideUnasignedTasks,
    #filtersModal .showAllMilestonesIcon {
        transition: filter 0.25s ease, transform 0.2s ease;
    }

    #filtersModal .showCompletedProjects:hover,
    #filtersModal .hideUnasignedTasks:hover,
    #filtersModal .showAllMilestonesIcon:hover {
        transform: scale(1.1);
    }

    #filtersModal .activeFiltersBar {
        margin-top: 20px;
        padding: 16px 18px;
        border: none;
        border-radius: 14px;
        background: #fafafa;
        border: 1px solid #c8c5c5;
    }

    #filtersModal .activeFiltersTitle {
        font-size: 10px;
        font-weight: 700;
        color: black;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin: 0 0 10px;
    }

    #filtersModal .activeFilterChip {
        background: #fff;
        border: 1px solid #e8e8e8;
        border-radius: 20px;
        padding: 5px 14px;
        font-size: 11px;
        font-weight: 600;
        color: #555;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    #filtersModal .activeFilterChip:hover {
        border-color: #ccc;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    }

    #filtersModal .activeFilterChip button {
        color: #ccc;
        font-size: 14px;
        transition: color 0.15s ease;
    }

    #filtersModal .activeFilterChip button:hover {
        color: #aa182c;
    }

    #filtersModal .filtersResetContainer {
        padding-top: 16px;
    }

    #filtersModal .filtersResetBtn {
        font-size: 14px;
        font-weight: 600;
        padding: 10px 22px;
        border-radius: 10px;
        background: #AA182C;
        border: none;
        color: #fff;
        letter-spacing: 0.3px;
        transition: background 0.2s ease, transform 0.15s ease;
    }

    #filtersModal .filtersApplyBtn {
        font-size: 14px;
        font-weight: 600;
        padding: 10px 22px;
        border-radius: 10px;
        background: #AA182C;
        border: none;
        color: #fff;
        letter-spacing: 0.3px;
        transition: background 0.2s ease, transform 0.15s ease;
    }

    #filtersModal .filtersResetBtn:hover {
        background: #7d0f1d;
        transform: scale(1.02);
    }

    #filtersModal .filtersApplyBtn:hover {
        background: #7d0f1d;
        transform: scale(1.02);
    }

    #filtersModal .projectSuggestions,
    #filtersModal .projectSuggestionItem,
    #filtersModal .projectSuggestionEmpty,
    #filtersModal .activeFiltersTitle,
    #filtersModal .activeFiltersList,
    #filtersModal .activeFilterChip,
    #filtersModal .activeFilterChip span {
        text-align: left;
        direction: ltr;
    }

    #filtersModal .activeFilterChip {
        justify-content: flex-start;
    }

    #filtersModal .dateRangeFilterLabel,
    #filtersModal .dateRangeFieldControl,
    #filtersModal .dateRangeFilterRow,
    #filtersModal .dateRangeFilterRow>div {
        text-align: left;
        direction: ltr;
    }
</style>

<div class="col-sm-auto" style="margin-right: 20px;">
    <button style="width: 100%" type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#filtersModal">
        <i class="fa-solid fa-filter me-3" style="color: #ffffff;"></i>
        {{ __('Filter Milestones') }}
    </button>
</div>

<div class="modal fade" id="filtersModal" tabindex="-1" aria-labelledby="filtersModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="filtersModalLabel">{{ __('Filter Milestones') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="filtersGrid">
                    <div class="filtersGridCol">
                        @if ($project_id == -1)
                            <div class="priorityFilterBlock">
                                <div id="showCompletedFilterToggle" class="priorityFilterHeader" role="button"
                                    aria-expanded="false" aria-controls="showCompletedFilterContent">
                                    <p class="filterLabel">{{ __('Completed Projects') }}</p>
                                    <i class="fa-solid fa-chevron-down priorityFilterChevron"></i>
                                </div>
                                <div id="showCompletedFilterContent" class="priorityFilterContent">
                                    <div class="toggleFilterContentInner">
                                        <div class="filterBinaryGroup" role="radiogroup"
                                            aria-label="{{ __('Completed Projects') }}">
                                            <label class="filterBinaryOption" for="showCompletedProjectsYes">
                                                <input type="radio" id="showCompletedProjectsYes"
                                                    name="showCompletedProjects" value="1">
                                                <span>{{ __('Show') }}</span>
                                            </label>
                                            <label class="filterBinaryOption" for="showCompletedProjectsNo">
                                                <input type="radio" id="showCompletedProjectsNo"
                                                    name="showCompletedProjects" value="0" checked>
                                                <span>{{ __('Hide') }}</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if ($project_id == -1)
                            <div class="projectFilterBlock">
                                <div id="projectFilterToggle" class="projectFilterHeader" role="button"
                                    aria-expanded="false" aria-controls="projectFilterContent">
                                    <p class="filterLabel">{{ __('Project Name') }}</p>
                                    <i class="fa-solid fa-chevron-down projectFilterChevron"></i>
                                </div>
                                <div id="projectFilterContent" class="projectFilterContent">
                                    <div class="projectFilterInputWrap">
                                        <div class="projectFilterAutocomplete">
                                            <input type="text" class="form-control" id="projectNameFilterInput"
                                                placeholder="{{ __('Search project') }}" autocomplete="off">
                                            <div id="projectSuggestions" class="projectSuggestions"></div>
                                        </div>
                                        <button type="button" class="btn btn-primary" id="applyProjectFilterBtn"
                                            title="{{ __('Apply project filter') }}">
                                            <i class="fa-solid fa-check"></i>
                                        </button>
                                    </div>
                                    <div id="selectedProjectList" class="selectedProjectList"></div>
                                </div>
                            </div>
                        @endif
                        <div class="priorityFilterBlock">
                            <div id="priorityFilterToggle" class="priorityFilterHeader" role="button"
                                aria-expanded="false" aria-controls="priorityFilterContent">
                                <p class="filterLabel">{{ __('Priority') }}</p>
                                <i class="fa-solid fa-chevron-down priorityFilterChevron"></i>
                            </div>
                            <div id="priorityFilterContent" class="priorityFilterContent">
                                <div class="priorityCheckboxList">
                                    <label class="priorityCheckboxItem">
                                        <input type="checkbox" class="priorityFilterCheckbox" value="high">
                                        <span class="priorityOptionLabel"
                                            data-base-label="{{ __('High Priority') }}">{{ __('High Priority') }}</span>
                                    </label>
                                    <label class="priorityCheckboxItem">
                                        <input type="checkbox" class="priorityFilterCheckbox" value="medium">
                                        <span class="priorityOptionLabel"
                                            data-base-label="{{ __('Medium Priority') }}">{{ __('Medium Priority') }}</span>
                                    </label>
                                    <label class="priorityCheckboxItem">
                                        <input type="checkbox" class="priorityFilterCheckbox" value="low">
                                        <span class="priorityOptionLabel"
                                            data-base-label="{{ __('Low Priority') }}">{{ __('Low Priority') }}</span>
                                    </label>
                                    <label class="priorityCheckboxItem">
                                        <input type="checkbox" class="priorityFilterCheckbox" value="none">
                                        <span class="priorityOptionLabel"
                                            data-base-label="{{ __('Not defined') }}">{{ __('Not defined') }}</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="filtersGridCol">
                        <div class="priorityFilterBlock">
                            <div id="hideUnassignedFilterToggle" class="priorityFilterHeader" role="button"
                                aria-expanded="false" aria-controls="hideUnassignedFilterContent">
                                <p class="filterLabel">{{ __('Unasigned Order Forms') }}</p>
                                <i class="fa-solid fa-chevron-down priorityFilterChevron"></i>
                            </div>
                            <div id="hideUnassignedFilterContent" class="priorityFilterContent">
                                <div class="toggleFilterContentInner">
                                    <div class="filterBinaryGroup" role="radiogroup"
                                        aria-label="{{ __('Unasigned Order Forms') }}">
                                        <label class="filterBinaryOption" for="hideUnassignedNo">
                                            <input type="radio" id="hideUnassignedNo" name="hideUnassigned"
                                                value="0" checked>
                                            <span>{{ __('Show') }}</span>
                                        </label>
                                        <label class="filterBinaryOption" for="hideUnassignedYes">
                                            <input type="radio" id="hideUnassignedYes" name="hideUnassigned"
                                                value="1">
                                            <span>{{ __('Hide') }}</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="projectFilterBlock">
                            <div id="requestedByFilterToggle" class="projectFilterHeader" role="button"
                                aria-expanded="false" aria-controls="requestedByFilterContent">
                                <p class="filterLabel">{{ __('Requested by') }}</p>
                                <i class="fa-solid fa-chevron-down projectFilterChevron"></i>
                            </div>
                            <div id="requestedByFilterContent" class="projectFilterContent">
                                <div class="projectFilterInputWrap">
                                    <div class="projectFilterAutocomplete">
                                        <input type="text" class="form-control" id="requestedByFilterInput"
                                            placeholder="{{ __('Search requester') }}" autocomplete="off">
                                        <div id="requestedBySuggestions" class="projectSuggestions"></div>
                                    </div>
                                    <button type="button" class="btn btn-primary" id="applyRequestedByFilterBtn"
                                        title="{{ __('Apply requester filter') }}">
                                        <i class="fa-solid fa-check"></i>
                                    </button>
                                </div>
                                <div id="selectedRequestedByList" class="selectedProjectList"></div>
                            </div>
                        </div>

                        @if ($project_id == -1)
                            <div class="priorityFilterBlock">
                                <div id="projectTypeFilterToggle" class="priorityFilterHeader" role="button"
                                    aria-expanded="false" aria-controls="projectTypeFilterContent">
                                    <p class="filterLabel">{{ __('Project type') }}</p>
                                    <i class="fa-solid fa-chevron-down priorityFilterChevron"></i>
                                </div>
                                <div id="projectTypeFilterContent" class="priorityFilterContent">
                                    <div id="projectTypeCheckboxList" class="priorityCheckboxList"></div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="filtersGridCol filtersGridCol--toggles">
                        @if ($project_id == -1)
                            @if ($filtersPopupMode === 'my_board')
                                <div class="projectFilterBlock">
                                    <div id="workspaceFilterToggle" class="projectFilterHeader" role="button"
                                        aria-expanded="false" aria-controls="workspaceFilterContent">
                                        <p class="filterLabel">{{ __('Workspace') }}</p>
                                        <i class="fa-solid fa-chevron-down projectFilterChevron"></i>
                                    </div>
                                    <div id="workspaceFilterContent" class="projectFilterContent">
                                        <div class="projectFilterInputWrap">
                                            <div class="projectFilterAutocomplete">
                                                <input type="text" class="form-control" id="workspaceFilterInput"
                                                    placeholder="{{ __('Search workspace') }}" autocomplete="off">
                                                <div id="workspaceSuggestions" class="projectSuggestions"></div>
                                            </div>
                                            <button type="button" class="btn btn-primary"
                                                id="applyWorkspaceFilterBtn"
                                                title="{{ __('Apply workspace filter') }}">
                                                <i class="fa-solid fa-check"></i>
                                            </button>
                                        </div>
                                        <div id="selectedWorkspaceList" class="selectedProjectList"></div>
                                    </div>
                                </div>
                            @else
                                <div class="priorityFilterBlock">
                                    <div id="showAllFilterToggle" class="priorityFilterHeader" role="button"
                                        aria-expanded="false" aria-controls="showAllFilterContent">
                                        <p class="filterLabel">{{ __('Workspace Milestones') }}</p>
                                        <i class="fa-solid fa-chevron-down priorityFilterChevron"></i>
                                    </div>
                                    <div id="showAllFilterContent" class="priorityFilterContent">
                                        <div class="toggleFilterContentInner">
                                            <div class="filterBinaryGroup" role="radiogroup"
                                                aria-label="{{ __('Workspace Milestones') }}">
                                                <label class="filterBinaryOption" for="showAllMilestonesYes">
                                                    <input type="radio" id="showAllMilestonesYes"
                                                        name="showAllMilestones" value="1" checked>
                                                    <span>{{ __('All') }}</span>
                                                </label>
                                                <label class="filterBinaryOption" for="showAllMilestonesNo">
                                                    <input type="radio" id="showAllMilestonesNo"
                                                        name="showAllMilestones" value="0">
                                                    <span>{{ __('Mine') }}</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endif

                        <div class="projectFilterBlock">
                            <div id="assignedToFilterToggle" class="projectFilterHeader" role="button"
                                aria-expanded="false" aria-controls="assignedToFilterContent">
                                <p class="filterLabel">{{ __('Assigned to') }}</p>
                                <i class="fa-solid fa-chevron-down projectFilterChevron"></i>
                            </div>
                            <div id="assignedToFilterContent" class="projectFilterContent">
                                <div class="projectFilterInputWrap">
                                    <div class="projectFilterAutocomplete">
                                        <input type="text" class="form-control" id="assignedToFilterInput"
                                            placeholder="{{ __('Search assignee') }}" autocomplete="off">
                                        <div id="assignedToSuggestions" class="projectSuggestions"></div>
                                    </div>
                                    <button type="button" class="btn btn-primary" id="applyAssignedToFilterBtn"
                                        title="{{ __('Apply assignee filter') }}">
                                        <i class="fa-solid fa-check"></i>
                                    </button>
                                </div>
                                <label class="priorityCheckboxItem mt-2" for="assignedToNoneCheckbox">
                                    <input type="checkbox" id="assignedToNoneCheckbox">
                                    <span>{{ __('None') }}</span>
                                </label>
                                <div id="selectedAssignedToList" class="selectedProjectList"></div>
                            </div>
                        </div>

                        <div class="priorityFilterBlock">
                            <div id="dateRangeFilterToggle" class="priorityFilterHeader" role="button"
                                aria-expanded="false" aria-controls="dateRangeFilterContent">
                                <p class="filterLabel">{{ __('Date range') }}</p>
                                <i class="fa-solid fa-chevron-down priorityFilterChevron"></i>
                            </div>
                            <div id="dateRangeFilterContent" class="priorityFilterContent">
                                <div class="dateRangeFilterGroup">
                                    <div class="dateRangeFieldControl">
                                        <p class="dateRangeFilterLabel">{{ __('Date field') }}</p>
                                        <div class="dateRangeFieldSelectWrap">
                                            <select id="dateRangeField" class="form-select form-select-sm">
                                                <option value="desired_delivery">{{ __('Desired delivery date') }}
                                                </option>
                                                <option value="planned_delivery">{{ __('Planned delivery date') }}
                                                </option>
                                                <option value="completed_date">{{ __('Completed date') }}</option>
                                            </select>
                                            <i class="fa-solid fa-chevron-down dateRangeFieldChevron"></i>
                                        </div>
                                    </div>

                                    <div class="dateRangeFilterRow">
                                        <div>
                                            <p class="dateRangeFilterLabel">{{ __('From') }}</p>
                                            <input type="date" id="dateRangeFrom"
                                                class="form-control form-control-sm">
                                        </div>
                                        <div>
                                            <p class="dateRangeFilterLabel">{{ __('To') }}</p>
                                            <input type="date" id="dateRangeTo"
                                                class="form-control form-control-sm">
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="activeFiltersBar" class="activeFiltersBar d-none">
                    <p class="activeFiltersTitle">{{ __('Active filters') }}</p>
                    <div id="activeFiltersList" class="activeFiltersList"></div>
                </div>

                <div class="filtersResetContainer">
                    <button type="button" id="resetFiltersBtn"
                        class="btn btn-primary btn-sm filtersResetBtn d-none">
                        {{ __('Reset filters') }}
                    </button>
                    <button type="button" id="applyFiltersBtn" class="btn btn-primary btn-sm filtersApplyBtn d-none"
                        data-bs-dismiss="modal">
                        {{ __('Apply') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const projectFilterBlock = document.querySelector('.projectFilterBlock');
        const projectFilterToggle = document.getElementById('projectFilterToggle');
        const projectFilterContent = document.getElementById('projectFilterContent');
        const projectFilterInput = document.getElementById('projectNameFilterInput');
        const applyProjectFilterBtn = document.getElementById('applyProjectFilterBtn');
        const projectSuggestions = document.getElementById('projectSuggestions');
        const selectedProjectList = document.getElementById('selectedProjectList');
        const workspaceFilterBlock = document.getElementById('workspaceFilterToggle') ? document
            .getElementById('workspaceFilterToggle').closest('.projectFilterBlock') : null;
        const workspaceFilterToggle = document.getElementById('workspaceFilterToggle');
        const workspaceFilterContent = document.getElementById('workspaceFilterContent');
        const workspaceFilterInput = document.getElementById('workspaceFilterInput');
        const applyWorkspaceFilterBtn = document.getElementById('applyWorkspaceFilterBtn');
        const workspaceSuggestions = document.getElementById('workspaceSuggestions');
        const selectedWorkspaceList = document.getElementById('selectedWorkspaceList');
        const requestedByFilterBlock = document.getElementById('requestedByFilterToggle') ? document
            .getElementById('requestedByFilterToggle').closest('.projectFilterBlock') : null;
        const requestedByFilterToggle = document.getElementById('requestedByFilterToggle');
        const requestedByFilterContent = document.getElementById('requestedByFilterContent');
        const requestedByFilterInput = document.getElementById('requestedByFilterInput');
        const applyRequestedByFilterBtn = document.getElementById('applyRequestedByFilterBtn');
        const requestedBySuggestions = document.getElementById('requestedBySuggestions');
        const selectedRequestedByList = document.getElementById('selectedRequestedByList');
        const assignedToFilterBlock = document.getElementById('assignedToFilterToggle') ? document
            .getElementById('assignedToFilterToggle').closest('.projectFilterBlock') : null;
        const assignedToFilterToggle = document.getElementById('assignedToFilterToggle');
        const assignedToFilterContent = document.getElementById('assignedToFilterContent');
        const assignedToFilterInput = document.getElementById('assignedToFilterInput');
        const applyAssignedToFilterBtn = document.getElementById('applyAssignedToFilterBtn');
        const assignedToSuggestions = document.getElementById('assignedToSuggestions');
        const selectedAssignedToList = document.getElementById('selectedAssignedToList');
        const assignedToNoneCheckbox = document.getElementById('assignedToNoneCheckbox');
        const priorityFilterBlock = document.getElementById('priorityFilterToggle') ? document
            .getElementById('priorityFilterToggle').closest('.priorityFilterBlock') : null;
        const priorityFilterToggle = document.getElementById('priorityFilterToggle');
        const priorityFilterContent = document.getElementById('priorityFilterContent');
        const priorityFilterCheckboxes = Array.from(document.querySelectorAll('.priorityFilterCheckbox'));
        const projectTypeFilterBlock = document.getElementById('projectTypeFilterToggle') ? document
            .getElementById('projectTypeFilterToggle').closest('.priorityFilterBlock') : null;
        const projectTypeFilterToggle = document.getElementById('projectTypeFilterToggle');
        const projectTypeFilterContent = document.getElementById('projectTypeFilterContent');
        const projectTypeCheckboxList = document.getElementById('projectTypeCheckboxList');
        const dateRangeFilterBlock = document.getElementById('dateRangeFilterToggle') ? document
            .getElementById('dateRangeFilterToggle').closest('.priorityFilterBlock') : null;
        const dateRangeFilterToggle = document.getElementById('dateRangeFilterToggle');
        const dateRangeFilterContent = document.getElementById('dateRangeFilterContent');
        const showCompletedFilterBlock = document.getElementById('showCompletedFilterToggle') ? document
            .getElementById('showCompletedFilterToggle').closest('.priorityFilterBlock') : null;
        const showCompletedFilterToggle = document.getElementById('showCompletedFilterToggle');
        const showCompletedFilterContent = document.getElementById('showCompletedFilterContent');
        const hideUnassignedFilterBlock = document.getElementById('hideUnassignedFilterToggle') ? document
            .getElementById('hideUnassignedFilterToggle').closest('.priorityFilterBlock') : null;
        const hideUnassignedFilterToggle = document.getElementById('hideUnassignedFilterToggle');
        const hideUnassignedFilterContent = document.getElementById('hideUnassignedFilterContent');
        const showAllFilterBlock = document.getElementById('showAllFilterToggle') ? document
            .getElementById('showAllFilterToggle').closest('.priorityFilterBlock') : null;
        const showAllFilterToggle = document.getElementById('showAllFilterToggle');
        const showAllFilterContent = document.getElementById('showAllFilterContent');
        const dateRangeField = document.getElementById('dateRangeField');
        const dateRangeFrom = document.getElementById('dateRangeFrom');
        const dateRangeTo = document.getElementById('dateRangeTo');
        const activeFiltersBar = document.getElementById('activeFiltersBar');
        const activeFiltersList = document.getElementById('activeFiltersList');
        const showCompletedProjectsYes = document.getElementById('showCompletedProjectsYes');
        const showCompletedProjectsNo = document.getElementById('showCompletedProjectsNo');
        const hideUnassignedYes = document.getElementById('hideUnassignedYes');
        const hideUnassignedNo = document.getElementById('hideUnassignedNo');
        const showAllMilestonesYes = document.getElementById('showAllMilestonesYes');
        const showAllMilestonesNo = document.getElementById('showAllMilestonesNo');
        const applyFiltersBtn = document.getElementById('applyFiltersBtn');
        const resetFiltersBtn = document.getElementById('resetFiltersBtn');
        const currentUserId = "{{ Auth::id() }}";
        const filtersPopupMode = @json($filtersPopupMode);
        const isMyBoardMode = filtersPopupMode === 'my_board';
        const isProjectScopedBoard = @json($project_id != -1);
        const hasCompletedFilter = !!showCompletedProjectsYes;
        const hasShowAllFilter = !isMyBoardMode && !!showAllMilestonesYes;
        let activeSuggestionIndex = -1;
        let activeWorkspaceSuggestionIndex = -1;
        let activeRequestedBySuggestionIndex = -1;
        let activeAssignedToSuggestionIndex = -1;

        const filtersState = {
            hideUnassigned: false,
            showAll: true,
            showCompleted: hasCompletedFilter ? false : true,
            selectedPriorities: [],
            selectedProjectTypes: [],
            selectedProjects: [],
            selectedWorkspaces: [],
            selectedRequestedBy: [],
            selectedAssignedTo: [],
            selectedAssignedToNone: false,
            dateField: 'desired_delivery',
            dateFrom: '',
            dateTo: '',
        };

        window.milestoneBoardFilters = filtersState;
        window.milestoneBoardShowCompleted = filtersState.showCompleted;

        window.addEventListener('beforeunload', function() {
            localStorage.setItem('milestoneBoardFilters', JSON.stringify({
                hideUnassigned: filtersState.hideUnassigned,
                showAll: filtersState.showAll,
                showCompleted: filtersState.showCompleted,
                selectedPriorities: filtersState.selectedPriorities,
                selectedProjectTypes: filtersState.selectedProjectTypes,
                selectedProjects: filtersState.selectedProjects,
                selectedWorkspaces: filtersState.selectedWorkspaces,
                selectedRequestedBy: filtersState.selectedRequestedBy,
                selectedAssignedTo: filtersState.selectedAssignedTo,
                selectedAssignedToNone: filtersState.selectedAssignedToNone,
                dateField: filtersState.dateField,
                dateFrom: filtersState.dateFrom,
                dateTo: filtersState.dateTo,
            }));
        });

        var savedFilters = localStorage.getItem('milestoneBoardFilters');
        if (savedFilters) {
            try {
                var parsed = JSON.parse(savedFilters);
                Object.keys(parsed).forEach(function(key) {
                    if (key in filtersState) {
                        filtersState[key] = parsed[key];
                    }
                });
            } catch(e) {}
            localStorage.removeItem('milestoneBoardFilters');
        }

        function syncBinaryFiltersUi() {
            if (hasCompletedFilter) {
                showCompletedProjectsYes.checked = !!filtersState.showCompleted;
                showCompletedProjectsNo.checked = !filtersState.showCompleted;
            }

            if (hideUnassignedYes && hideUnassignedNo) {
                hideUnassignedYes.checked = !!filtersState.hideUnassigned;
                hideUnassignedNo.checked = !filtersState.hideUnassigned;
            }

            if (hasShowAllFilter) {
                showAllMilestonesYes.checked = !!filtersState.showAll;
                showAllMilestonesNo.checked = !filtersState.showAll;
            }
        }

        if (projectFilterBlock && projectFilterToggle && projectFilterContent) {
            projectFilterBlock.classList.remove('open');
            projectFilterToggle.setAttribute('aria-expanded', 'false');

            projectFilterToggle.addEventListener('click', function() {
                const willOpen = !projectFilterBlock.classList.contains('open');
                projectFilterBlock.classList.toggle('open', willOpen);
                projectFilterToggle.setAttribute('aria-expanded', willOpen ? 'true' : 'false');

                if (willOpen && projectFilterInput) {
                    projectFilterInput.focus();
                    renderProjectSuggestions(projectFilterInput.value || '');
                } else {
                    hideProjectSuggestions();
                }
            });
        }

        if (workspaceFilterBlock && workspaceFilterToggle && workspaceFilterContent) {
            const workspaceDefaultOpen = isMyBoardMode && !isProjectScopedBoard;
            workspaceFilterBlock.classList.toggle('open', workspaceDefaultOpen);
            workspaceFilterToggle.setAttribute('aria-expanded', workspaceDefaultOpen ? 'true' : 'false');

            workspaceFilterToggle.addEventListener('click', function() {
                const willOpen = !workspaceFilterBlock.classList.contains('open');
                workspaceFilterBlock.classList.toggle('open', willOpen);
                workspaceFilterToggle.setAttribute('aria-expanded', willOpen ? 'true' : 'false');

                if (willOpen && workspaceFilterInput) {
                    workspaceFilterInput.focus();
                    renderWorkspaceSuggestions(workspaceFilterInput.value || '');
                } else {
                    hideWorkspaceSuggestions();
                }
            });
        }

        if (requestedByFilterBlock && requestedByFilterToggle && requestedByFilterContent) {
            requestedByFilterBlock.classList.remove('open');
            requestedByFilterToggle.setAttribute('aria-expanded', 'false');

            requestedByFilterToggle.addEventListener('click', function() {
                const willOpen = !requestedByFilterBlock.classList.contains('open');
                requestedByFilterBlock.classList.toggle('open', willOpen);
                requestedByFilterToggle.setAttribute('aria-expanded', willOpen ? 'true' : 'false');

                if (willOpen && requestedByFilterInput) {
                    requestedByFilterInput.focus();
                    renderRequestedBySuggestions(requestedByFilterInput.value || '');
                } else {
                    hideRequestedBySuggestions();
                }
            });
        }

        if (assignedToFilterBlock && assignedToFilterToggle && assignedToFilterContent) {
            const assignedToDefaultOpen = isProjectScopedBoard;
            assignedToFilterBlock.classList.toggle('open', assignedToDefaultOpen);
            assignedToFilterToggle.setAttribute('aria-expanded', assignedToDefaultOpen ? 'true' : 'false');

            assignedToFilterToggle.addEventListener('click', function() {
                const willOpen = !assignedToFilterBlock.classList.contains('open');
                assignedToFilterBlock.classList.toggle('open', willOpen);
                assignedToFilterToggle.setAttribute('aria-expanded', willOpen ? 'true' : 'false');

                if (willOpen && assignedToFilterInput) {
                    assignedToFilterInput.focus();
                    renderAssignedToSuggestions(assignedToFilterInput.value || '');
                } else {
                    hideAssignedToSuggestions();
                }
            });
        }

        if (priorityFilterBlock && priorityFilterToggle && priorityFilterContent) {
            const priorityDefaultOpen = isMyBoardMode ? false : isProjectScopedBoard;
            priorityFilterBlock.classList.toggle('open', priorityDefaultOpen);
            priorityFilterToggle.setAttribute('aria-expanded', priorityDefaultOpen ? 'true' : 'false');

            priorityFilterToggle.addEventListener('click', function() {
                const willOpen = !priorityFilterBlock.classList.contains('open');
                priorityFilterBlock.classList.toggle('open', willOpen);
                priorityFilterToggle.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
            });
        }

        if (projectTypeFilterBlock && projectTypeFilterToggle && projectTypeFilterContent) {
            projectTypeFilterBlock.classList.remove('open');
            projectTypeFilterToggle.setAttribute('aria-expanded', 'false');

            projectTypeFilterToggle.addEventListener('click', function() {
                const willOpen = !projectTypeFilterBlock.classList.contains('open');
                projectTypeFilterBlock.classList.toggle('open', willOpen);
                projectTypeFilterToggle.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
            });
        }

        if (dateRangeFilterBlock && dateRangeFilterToggle && dateRangeFilterContent) {
            dateRangeFilterBlock.classList.remove('open');
            dateRangeFilterToggle.setAttribute('aria-expanded', 'false');

            dateRangeFilterToggle.addEventListener('click', function() {
                const willOpen = !dateRangeFilterBlock.classList.contains('open');
                dateRangeFilterBlock.classList.toggle('open', willOpen);
                dateRangeFilterToggle.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
            });
        }

        if (showCompletedFilterBlock && showCompletedFilterToggle && showCompletedFilterContent) {
            showCompletedFilterBlock.classList.add('open');
            showCompletedFilterToggle.setAttribute('aria-expanded', 'true');

            showCompletedFilterToggle.addEventListener('click', function() {
                const willOpen = !showCompletedFilterBlock.classList.contains('open');
                showCompletedFilterBlock.classList.toggle('open', willOpen);
                showCompletedFilterToggle.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
            });
        }

        if (hideUnassignedFilterBlock && hideUnassignedFilterToggle && hideUnassignedFilterContent) {
            hideUnassignedFilterBlock.classList.add('open');
            hideUnassignedFilterToggle.setAttribute('aria-expanded', 'true');

            hideUnassignedFilterToggle.addEventListener('click', function() {
                const willOpen = !hideUnassignedFilterBlock.classList.contains('open');
                hideUnassignedFilterBlock.classList.toggle('open', willOpen);
                hideUnassignedFilterToggle.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
            });
        }

        if (showAllFilterBlock && showAllFilterToggle && showAllFilterContent) {
            showAllFilterBlock.classList.add('open');
            showAllFilterToggle.setAttribute('aria-expanded', 'true');

            showAllFilterToggle.addEventListener('click', function() {
                const willOpen = !showAllFilterBlock.classList.contains('open');
                showAllFilterBlock.classList.toggle('open', willOpen);
                showAllFilterToggle.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
            });
        }

        function groupMilestonesByProject(cards) {
            const map = new Map();
            cards.forEach(card => {
                const projectId = card.dataset.projectId;
                if (!map.has(projectId)) {
                    map.set(projectId, []);
                }
                map.get(projectId).push(card);
            });
            return map;
        }

        function normalizeProjectName(name) {
            return (name || '').trim().toLowerCase();
        }

        function normalizeWorkspaceName(name) {
            return (name || '').trim().toLowerCase();
        }

        function normalizePriority(value) {
            const normalized = (value || '').trim().toLowerCase();
            if (normalized === 'alta' || normalized === 'high') {
                return 'high';
            }
            if (normalized === 'media' || normalized === 'medium') {
                return 'medium';
            }
            if (normalized === 'baja' || normalized === 'low') {
                return 'low';
            }
            return '';
        }

        function getAllProjectNames() {
            const cards = document.querySelectorAll('.card[data-project-id]');
            const names = new Set();

            cards.forEach(card => {
                const name = (card.dataset.projectName || '').trim();
                if (name) {
                    names.add(name);
                }
            });

            return Array.from(names).sort((a, b) => a.localeCompare(b));
        }

        function getAllWorkspaceNames() {
            const cards = document.querySelectorAll('.card[data-project-id]');
            const names = new Set();

            cards.forEach(card => {
                const name = (card.dataset.workspaceName || '').trim();
                if (name) {
                    names.add(name);
                }
            });

            return Array.from(names).sort((a, b) => a.localeCompare(b));
        }

        function normalizeRequestedBy(value) {
            return (value || '').toString().trim();
        }

        function normalizeProjectType(value) {
            return (value || '').toString().trim().toLowerCase();
        }

        function formatProjectTypeLabel(value) {
            return (value || '')
                .replace(/[_-]+/g, ' ')
                .split(' ')
                .filter(Boolean)
                .map(token => token.charAt(0).toUpperCase() + token.slice(1))
                .join(' ');
        }

        function getProjectTypeLabel(value) {
            const normalized = normalizeProjectType(value);
            const options = getAllProjectTypeOptions();
            const matchedOption = options.find(option => normalizeProjectType(option.value) === normalized);

            if (matchedOption && matchedOption.label) {
                return matchedOption.label;
            }

            return formatProjectTypeLabel(value);
        }

        function normalizeRequestedByLabel(value) {
            return (value || '').toString().trim().toLowerCase();
        }

        function normalizeDateValue(value) {
            const raw = (value || '').toString().trim();
            if (!raw || raw === '0000-00-00') {
                return '';
            }

            const datePart = raw.slice(0, 10);
            return /^\d{4}-\d{2}-\d{2}$/.test(datePart) ? datePart : '';
        }

        function getDateObject(value) {
            const normalized = normalizeDateValue(value);
            if (!normalized) {
                return null;
            }

            const date = new Date(`${normalized}T00:00:00`);
            return Number.isNaN(date.getTime()) ? null : date;
        }

        function getCardDateByField(card, field) {
            if (field === 'planned_delivery') {
                return normalizeDateValue(card.dataset.plannedDeliveryDate || '');
            }

            if (field === 'completed_date') {
                return normalizeDateValue(card.dataset.completedDate || '');
            }

            return normalizeDateValue(card.dataset.desiredDeliveryDate || '');
        }

        function matchesDateRange(card) {
            const selectedField = filtersState.dateField || 'desired_delivery';
            const from = normalizeDateValue(filtersState.dateFrom);
            const to = normalizeDateValue(filtersState.dateTo);

            if (!from && !to) {
                return true;
            }

            const cardDateValue = getCardDateByField(card, selectedField);
            if (!cardDateValue) {
                return false;
            }

            const cardDate = getDateObject(cardDateValue);
            if (!cardDate) {
                return false;
            }

            if (from) {
                const fromDate = getDateObject(from);
                if (fromDate && cardDate < fromDate) {
                    return false;
                }
            }

            if (to) {
                const toDate = getDateObject(to);
                if (toDate && cardDate > toDate) {
                    return false;
                }
            }

            return true;
        }

        function formatDateChipLabel(value) {
            const normalized = normalizeDateValue(value);
            if (!normalized) {
                return '';
            }

            const parts = normalized.split('-');
            if (parts.length !== 3) {
                return normalized;
            }

            return `${parts[2]}-${parts[1]}-${parts[0]}`;
        }

        function getDateFieldDisplayLabel(field) {
            if (field === 'planned_delivery') {
                return "{{ __('Planned delivery date') }}";
            }

            if (field === 'completed_date') {
                return "{{ __('Completed date') }}";
            }

            return "{{ __('Desired delivery date') }}";
        }

        function renderActiveFiltersChips() {
            if (!activeFiltersBar || !activeFiltersList) {
                return;
            }

            const chips = [];

            if (filtersState.hideUnassigned) {
                chips.push({
                    type: 'hideUnassigned',
                    value: '1',
                    label: "{{ __('Unasigned Order Forms') }}",
                });
            }

            if (hasShowAllFilter && !filtersState.showAll) {
                chips.push({
                    type: 'showAll',
                    value: '0',
                    label: "{{ __('Workspace Milestones') }}: {{ __('Mine') }}",
                });
            }

            if (hasCompletedFilter && filtersState.showCompleted) {
                chips.push({
                    type: 'showCompleted',
                    value: '1',
                    label: "{{ __('Completed Projects') }}",
                });
            }

            filtersState.selectedPriorities.forEach(priority => {
                let label = "{{ __('Not defined') }}";
                if (priority === 'high') {
                    label = "{{ __('High Priority') }}";
                } else if (priority === 'medium') {
                    label = "{{ __('Medium Priority') }}";
                } else if (priority === 'low') {
                    label = "{{ __('Low Priority') }}";
                }

                chips.push({
                    type: 'priority',
                    value: priority,
                    label,
                });
            });

            filtersState.selectedProjectTypes.forEach(projectType => {
                chips.push({
                    type: 'projectType',
                    value: projectType,
                    label: `${"{{ __('Project type') }}"}: ${getProjectTypeLabel(projectType)}`,
                });
            });

            filtersState.selectedProjects.forEach(projectName => {
                chips.push({
                    type: 'project',
                    value: projectName,
                    label: `${"{{ __('Project Name') }}"}: ${projectName}`,
                });
            });

            if (isMyBoardMode) {
                filtersState.selectedWorkspaces.forEach(workspaceName => {
                    chips.push({
                        type: 'workspace',
                        value: workspaceName,
                        label: `${"{{ __('Workspace') }}"}: ${workspaceName}`,
                    });
                });
            }

            filtersState.selectedRequestedBy.forEach(requestedById => {
                chips.push({
                    type: 'requestedBy',
                    value: requestedById,
                    label: `${"{{ __('Requested by') }}"}: ${getRequestedByLabel(requestedById)}`,
                });
            });

            filtersState.selectedAssignedTo.forEach(assignedToId => {
                chips.push({
                    type: 'assignedTo',
                    value: assignedToId,
                    label: `${"{{ __('Assigned to') }}"}: ${getAssignedToLabel(assignedToId)}`,
                });
            });

            if (filtersState.selectedAssignedToNone) {
                chips.push({
                    type: 'assignedToNone',
                    value: 'none',
                    label: `${"{{ __('Assigned to') }}"}: {{ __('None') }}`,
                });
            }

            if (filtersState.dateFrom || filtersState.dateTo) {
                const fromLabel = formatDateChipLabel(filtersState.dateFrom);
                const toLabel = formatDateChipLabel(filtersState.dateTo);
                let rangeLabel = getDateFieldDisplayLabel(filtersState.dateField);

                if (fromLabel && toLabel) {
                    rangeLabel = `${rangeLabel}: ${fromLabel} → ${toLabel}`;
                } else if (fromLabel) {
                    rangeLabel = `${rangeLabel}: ${"{{ __('From') }}"} ${fromLabel}`;
                } else if (toLabel) {
                    rangeLabel = `${rangeLabel}: ${"{{ __('To') }}"} ${toLabel}`;
                }

                chips.push({
                    type: 'dateRange',
                    value: 'dateRange',
                    label: rangeLabel,
                });
            }

            const chipsPriority = {
                dateRange: 1,
                priority: 2,
                assignedTo: 3,
                workspace: 4,
                project: 5,
                requestedBy: 6,
                projectType: 7,
                hideUnassigned: 8,
                showAll: 8,
                showCompleted: 9,
            };

            chips.sort((a, b) => {
                const rankA = chipsPriority[a.type] || 99;
                const rankB = chipsPriority[b.type] || 99;

                if (rankA !== rankB) {
                    return rankA - rankB;
                }

                return String(a.label || '').localeCompare(String(b.label || ''));
            });

            if (!chips.length) {
                activeFiltersList.innerHTML = '';
                activeFiltersBar.classList.add('d-none');
                return;
            }

            const maxVisibleChips = 4;
            const visibleChips = chips.slice(0, maxVisibleChips);
            const hiddenCount = chips.length - visibleChips.length;

            if (hiddenCount > 0) {
                const moreLabel = hiddenCount === 1 ?
                    `${hiddenCount} {{ __('more filter') }}` :
                    `+${hiddenCount} {{ __('more filters') }}`;

                visibleChips.push({
                    type: 'summary',
                    value: 'summary',
                    label: moreLabel,
                    removable: false,
                });
            }

            activeFiltersList.innerHTML = '';
            visibleChips.forEach(chip => {
                const chipNode = document.createElement('span');
                chipNode.className = 'activeFilterChip';

                const chipText = document.createElement('span');
                chipText.textContent = chip.label;
                chipNode.appendChild(chipText);

                if (chip.removable !== false) {
                    const removeButton = document.createElement('button');
                    removeButton.type = 'button';
                    removeButton.setAttribute('data-chip-type', chip.type);
                    removeButton.setAttribute('data-chip-value', String(chip.value));
                    removeButton.textContent = '×';
                    chipNode.appendChild(removeButton);
                }

                activeFiltersList.appendChild(chipNode);
            });
            activeFiltersBar.classList.remove('d-none');
        }

        function getAllRequestedByOptions() {
            const cards = document.querySelectorAll('.card[data-project-id]');
            const optionsMap = new Map();

            cards.forEach(card => {
                const requesterId = normalizeRequestedBy(card.dataset.requestedBy || '');
                if (!requesterId) {
                    return;
                }

                const requesterName = (card.dataset.requestedByName || '').trim();
                const label = requesterName || requesterId;

                if (!optionsMap.has(requesterId)) {
                    optionsMap.set(requesterId, label);
                }
            });

            return Array.from(optionsMap.entries())
                .map(([id, label]) => ({
                    id,
                    label
                }))
                .sort((a, b) => a.label.localeCompare(b.label));
        }

        function getAllAssignedToOptions() {
            const cards = document.querySelectorAll('.card[data-project-id]');
            const optionsMap = new Map();

            cards.forEach(card => {
                const assignedId = normalizeRequestedBy(card.dataset.assignTo || '');
                if (!assignedId) {
                    return;
                }

                const assignedName = (card.dataset.assignToName || '').trim();
                const label = assignedName || assignedId;

                if (!optionsMap.has(assignedId)) {
                    optionsMap.set(assignedId, label);
                }
            });

            return Array.from(optionsMap.entries())
                .map(([id, label]) => ({
                    id,
                    label
                }))
                .sort((a, b) => a.label.localeCompare(b.label));
        }

        function getAllProjectTypeOptions() {
            const cards = document.querySelectorAll('.card[data-project-id]');
            const optionsMap = new Map();

            cards.forEach(card => {
                const type = normalizeProjectType(card.dataset.projectType || '');
                if (!type) {
                    return;
                }

                const translatedLabel = (card.dataset.projectTypeLabel || '').trim();
                const fallbackLabel = formatProjectTypeLabel(card.dataset.projectType || type);
                const label = translatedLabel || fallbackLabel;

                if (!optionsMap.has(type)) {
                    optionsMap.set(type, label);
                }
            });

            return Array.from(optionsMap.entries())
                .map(([value, label]) => ({
                    value,
                    label,
                }))
                .sort((a, b) => a.label.localeCompare(b.label));
        }

        /**
         * Build normalized sets/arrays used by filtering to avoid duplicating conversion logic.
         */
        function getFilterCriteria() {
            return {
                selectedProjectNames: filtersState.selectedProjects.map(name => normalizeProjectName(name)),
                selectedWorkspaceNames: filtersState.selectedWorkspaces.map(name => normalizeWorkspaceName(
                    name)),
                selectedPriorities: new Set(filtersState.selectedPriorities),
                selectedProjectTypes: new Set(filtersState.selectedProjectTypes.map(value =>
                    normalizeProjectType(value))),
                selectedRequestedBySet: new Set(filtersState.selectedRequestedBy.map(value =>
                    normalizeRequestedBy(value))),
                selectedAssignedToSet: new Set(filtersState.selectedAssignedTo.map(value =>
                    normalizeRequestedBy(value))),
                selectedAssignedToNone: !!filtersState.selectedAssignedToNone,
            };
        }

        /**
         * A project is considered completed when every visible milestone card in that project has status 4.
         */
        function isCompletedProject(card, groupedByProject) {
            const projectId = card.dataset.projectId;
            const projectMilestones = groupedByProject.get(projectId) || [];
            return projectMilestones.length > 0 && projectMilestones.every(m =>
                parseInt(m.dataset.status, 10) === 4);
        }

        /**
         * Central visibility predicate used by both count helpers and final board rendering.
         */
        function cardMatchesFilters(card, groupedByProject, criteria, ignoreFilter = '') {
            const projectName = (card.dataset.projectName || '').trim();
            const workspaceName = (card.dataset.workspaceName || '').trim();
            const projectType = normalizeProjectType(card.dataset.projectType || '');
            const priority = normalizePriority(card.dataset.priority || '');
            const priorityKey = priority || 'none';
            const requestedBy = normalizeRequestedBy(card.dataset.requestedBy || '');
            const assignedTo = normalizeRequestedBy(card.dataset.assignTo || '');
            const isUnassigned = card.classList.contains('notAsignedMilestone');
            const allInStatus4 = isCompletedProject(card, groupedByProject);

            if (!isMyBoardMode && !filtersState.showAll && !isMine(card)) {
                return false;
            }

            if (filtersState.hideUnassigned && isUnassigned) {
                return false;
            }

            if (ignoreFilter !== 'projectName' && criteria.selectedProjectNames.length > 0 &&
                !criteria.selectedProjectNames.includes(normalizeProjectName(projectName))) {
                return false;
            }

            if (isMyBoardMode && ignoreFilter !== 'workspace' && criteria.selectedWorkspaceNames.length > 0 &&
                !criteria.selectedWorkspaceNames.includes(normalizeWorkspaceName(workspaceName))) {
                return false;
            }

            if (ignoreFilter !== 'priority' && criteria.selectedPriorities.size > 0 &&
                !criteria.selectedPriorities.has(priorityKey)) {
                return false;
            }

            if (ignoreFilter !== 'projectType' && criteria.selectedProjectTypes.size > 0 &&
                !criteria.selectedProjectTypes.has(projectType)) {
                return false;
            }

            if (ignoreFilter !== 'requestedBy' && criteria.selectedRequestedBySet.size > 0 &&
                !criteria.selectedRequestedBySet.has(requestedBy)) {
                return false;
            }

            if (ignoreFilter !== 'assignedTo') {
                const hasAssignedToFilter = criteria.selectedAssignedToSet.size > 0 || criteria
                    .selectedAssignedToNone;

                if (hasAssignedToFilter) {
                    const matchesAssignedTo = criteria.selectedAssignedToSet.has(assignedTo);
                    const matchesUnassigned = criteria.selectedAssignedToNone && (!assignedTo ||
                        isUnassigned);

                    if (!matchesAssignedTo && !matchesUnassigned) {
                        return false;
                    }
                }
            }

            if (ignoreFilter !== 'dateRange' && !matchesDateRange(card)) {
                return false;
            }

            if (hasCompletedFilter && !filtersState.showCompleted && allInStatus4) {
                return false;
            }

            return true;
        }

        function getCardsPassingFilters(ignoreFilter = '') {
            const allMilestones = Array.from(document.querySelectorAll('.card[data-project-id]'));
            const groupedByProject = groupMilestonesByProject(allMilestones);
            const criteria = getFilterCriteria();

            return allMilestones.filter(card => cardMatchesFilters(card, groupedByProject, criteria,
                ignoreFilter));
        }

        function getPriorityCounts() {
            const cards = getCardsPassingFilters('priority');
            const counts = {
                high: 0,
                medium: 0,
                low: 0,
                none: 0,
            };

            cards.forEach(card => {
                const priority = normalizePriority(card.dataset.priority || '') || 'none';
                if (counts[priority] !== undefined) {
                    counts[priority] += 1;
                }
            });

            return counts;
        }

        function getProjectTypeCounts() {
            const cards = getCardsPassingFilters('projectType');
            const counts = new Map();

            cards.forEach(card => {
                const type = normalizeProjectType(card.dataset.projectType || '');
                if (!type) {
                    return;
                }

                counts.set(type, (counts.get(type) || 0) + 1);
            });

            return counts;
        }

        function getProjectNameCounts() {
            const cards = getCardsPassingFilters('projectName');
            const counts = new Map();

            cards.forEach(card => {
                const label = (card.dataset.projectName || '').trim();
                if (!label) {
                    return;
                }

                const key = normalizeProjectName(label);
                const current = counts.get(key) || {
                    label,
                    count: 0
                };
                current.count += 1;
                counts.set(key, current);
            });

            return counts;
        }

        function getWorkspaceCounts() {
            const cards = getCardsPassingFilters('workspace');
            const counts = new Map();

            cards.forEach(card => {
                const label = (card.dataset.workspaceName || '').trim();
                if (!label) {
                    return;
                }

                const key = normalizeWorkspaceName(label);
                const current = counts.get(key) || {
                    label,
                    count: 0
                };
                current.count += 1;
                counts.set(key, current);
            });

            return counts;
        }

        function getRequestedByCounts() {
            const cards = getCardsPassingFilters('requestedBy');
            const counts = new Map();

            cards.forEach(card => {
                const id = normalizeRequestedBy(card.dataset.requestedBy || '');
                if (!id) {
                    return;
                }

                const label = (card.dataset.requestedByName || '').trim() || id;
                const current = counts.get(id) || {
                    label,
                    count: 0
                };
                current.count += 1;
                counts.set(id, current);
            });

            return counts;
        }

        function getAssignedToCounts() {
            const cards = getCardsPassingFilters('assignedTo');
            const counts = new Map();

            cards.forEach(card => {
                const id = normalizeRequestedBy(card.dataset.assignTo || '');
                if (!id) {
                    return;
                }

                const label = (card.dataset.assignToName || '').trim() || id;
                const current = counts.get(id) || {
                    label,
                    count: 0
                };
                current.count += 1;
                counts.set(id, current);
            });

            return counts;
        }

        function updatePriorityOptionCounts() {
            const counts = getPriorityCounts();
            priorityFilterCheckboxes.forEach(checkbox => {
                const labelNode = checkbox.parentElement ? checkbox.parentElement.querySelector(
                    '.priorityOptionLabel') : null;
                if (!labelNode) {
                    return;
                }

                const baseLabel = labelNode.getAttribute('data-base-label') || labelNode.textContent;
                const count = counts[checkbox.value] || 0;
                labelNode.textContent = `${baseLabel} (${count})`;
            });
        }

        function renderProjectTypeCheckboxes() {
            if (!projectTypeCheckboxList) {
                return;
            }

            const selectedSet = new Set(filtersState.selectedProjectTypes.map(value => normalizeProjectType(
                value)));
            const options = getAllProjectTypeOptions();
            const counts = getProjectTypeCounts();

            projectTypeCheckboxList.innerHTML = '';

            options.forEach(option => {
                const count = counts.get(option.value) || 0;
                const item = document.createElement('label');
                item.className = 'priorityCheckboxItem';

                const checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.className = 'projectTypeFilterCheckbox';
                checkbox.value = option.value;
                checkbox.checked = selectedSet.has(option.value);

                const label = document.createElement('span');
                label.textContent = `${option.label} (${count})`;

                item.appendChild(checkbox);
                item.appendChild(label);
                projectTypeCheckboxList.appendChild(item);
            });
        }

        function hideProjectSuggestions() {
            if (!projectSuggestions) {
                return;
            }

            activeSuggestionIndex = -1;
            projectSuggestions.classList.remove('visible');
            projectSuggestions.innerHTML = '';
        }

        function hideWorkspaceSuggestions() {
            if (!workspaceSuggestions) {
                return;
            }

            activeWorkspaceSuggestionIndex = -1;
            workspaceSuggestions.classList.remove('visible');
            workspaceSuggestions.innerHTML = '';
        }

        function hideRequestedBySuggestions() {
            if (!requestedBySuggestions) {
                return;
            }

            activeRequestedBySuggestionIndex = -1;
            requestedBySuggestions.classList.remove('visible');
            requestedBySuggestions.innerHTML = '';
        }

        function hideAssignedToSuggestions() {
            if (!assignedToSuggestions) {
                return;
            }

            activeAssignedToSuggestionIndex = -1;
            assignedToSuggestions.classList.remove('visible');
            assignedToSuggestions.innerHTML = '';
        }

        function getSuggestionItems() {
            if (!projectSuggestions) {
                return [];
            }

            return Array.from(projectSuggestions.querySelectorAll(
                '.projectSuggestionItem:not(.projectSuggestionEmpty)'));
        }

        function getWorkspaceSuggestionItems() {
            if (!workspaceSuggestions) {
                return [];
            }

            return Array.from(workspaceSuggestions.querySelectorAll(
                '.projectSuggestionItem:not(.projectSuggestionEmpty)'));
        }

        function getRequestedBySuggestionItems() {
            if (!requestedBySuggestions) {
                return [];
            }

            return Array.from(requestedBySuggestions.querySelectorAll(
                '.projectSuggestionItem:not(.projectSuggestionEmpty)'));
        }

        function getAssignedToSuggestionItems() {
            if (!assignedToSuggestions) {
                return [];
            }

            return Array.from(assignedToSuggestions.querySelectorAll(
                '.projectSuggestionItem:not(.projectSuggestionEmpty)'));
        }

        function renderNoMatchSuggestion(container) {
            if (!container) {
                return;
            }

            container.innerHTML = '';
            const item = document.createElement('div');
            item.className = 'projectSuggestionItem projectSuggestionEmpty';
            item.textContent = "{{ __('No match found') }}";
            container.appendChild(item);
            container.classList.add('visible');
        }

        function setActiveSuggestion(index) {
            const items = getSuggestionItems();
            if (!items.length) {
                activeSuggestionIndex = -1;
                return;
            }

            if (index < 0) {
                index = items.length - 1;
            }

            if (index >= items.length) {
                index = 0;
            }

            activeSuggestionIndex = index;

            items.forEach((item, itemIndex) => {
                item.classList.toggle('active', itemIndex === activeSuggestionIndex);
            });

            const activeItem = items[activeSuggestionIndex];
            if (activeItem) {
                activeItem.scrollIntoView({
                    block: 'nearest'
                });
            }
        }

        function setActiveWorkspaceSuggestion(index) {
            const items = getWorkspaceSuggestionItems();
            if (!items.length) {
                activeWorkspaceSuggestionIndex = -1;
                return;
            }

            if (index < 0) {
                index = items.length - 1;
            }

            if (index >= items.length) {
                index = 0;
            }

            activeWorkspaceSuggestionIndex = index;

            items.forEach((item, itemIndex) => {
                item.classList.toggle('active', itemIndex === activeWorkspaceSuggestionIndex);
            });

            const activeItem = items[activeWorkspaceSuggestionIndex];
            if (activeItem) {
                activeItem.scrollIntoView({
                    block: 'nearest'
                });
            }
        }

        function setActiveRequestedBySuggestion(index) {
            const items = getRequestedBySuggestionItems();
            if (!items.length) {
                activeRequestedBySuggestionIndex = -1;
                return;
            }

            if (index < 0) {
                index = items.length - 1;
            }

            if (index >= items.length) {
                index = 0;
            }

            activeRequestedBySuggestionIndex = index;

            items.forEach((item, itemIndex) => {
                item.classList.toggle('active', itemIndex === activeRequestedBySuggestionIndex);
            });

            const activeItem = items[activeRequestedBySuggestionIndex];
            if (activeItem) {
                activeItem.scrollIntoView({
                    block: 'nearest'
                });
            }
        }

        function setActiveAssignedToSuggestion(index) {
            const items = getAssignedToSuggestionItems();
            if (!items.length) {
                activeAssignedToSuggestionIndex = -1;
                return;
            }

            if (index < 0) {
                index = items.length - 1;
            }

            if (index >= items.length) {
                index = 0;
            }

            activeAssignedToSuggestionIndex = index;

            items.forEach((item, itemIndex) => {
                item.classList.toggle('active', itemIndex === activeAssignedToSuggestionIndex);
            });

            const activeItem = items[activeAssignedToSuggestionIndex];
            if (activeItem) {
                activeItem.scrollIntoView({
                    block: 'nearest'
                });
            }
        }

        function renderProjectSuggestions(query = '') {
            if (!projectSuggestions || !projectFilterInput) {
                return;
            }

            const nameCounts = getProjectNameCounts();
            const names = Array.from(nameCounts.values())
                .sort((a, b) => a.label.localeCompare(b.label));
            const normalizedQuery = normalizeProjectName(query);
            const alreadySelected = filtersState.selectedProjects.map(name => normalizeProjectName(name));

            const filtered = names.filter(item => {
                const normalizedName = normalizeProjectName(item.label);
                if (alreadySelected.includes(normalizedName)) {
                    return false;
                }
                if (!normalizedQuery) {
                    return true;
                }
                return normalizedName.includes(normalizedQuery);
            }).slice(0, 12);

            projectSuggestions.innerHTML = '';

            if (!filtered.length) {
                activeSuggestionIndex = -1;
                renderNoMatchSuggestion(projectSuggestions);
                return;
            }

            filtered.forEach(itemData => {
                const item = document.createElement('div');
                item.className = 'projectSuggestionItem';
                item.textContent = `${itemData.label} (${itemData.count})`;
                item.setAttribute('data-project-name', itemData.label);
                projectSuggestions.appendChild(item);
            });

            activeSuggestionIndex = -1;

            projectSuggestions.classList.add('visible');
        }

        function renderWorkspaceSuggestions(query = '') {
            if (!workspaceSuggestions || !workspaceFilterInput) {
                return;
            }

            const workspaceCounts = getWorkspaceCounts();
            const workspaces = Array.from(workspaceCounts.values())
                .sort((a, b) => a.label.localeCompare(b.label));
            const normalizedQuery = normalizeWorkspaceName(query);
            const alreadySelected = filtersState.selectedWorkspaces.map(name => normalizeWorkspaceName(name));

            const filtered = workspaces.filter(item => {
                const normalizedName = normalizeWorkspaceName(item.label);
                if (alreadySelected.includes(normalizedName)) {
                    return false;
                }
                if (!normalizedQuery) {
                    return true;
                }
                return normalizedName.includes(normalizedQuery);
            }).slice(0, 12);

            workspaceSuggestions.innerHTML = '';

            if (!filtered.length) {
                activeWorkspaceSuggestionIndex = -1;
                renderNoMatchSuggestion(workspaceSuggestions);
                return;
            }

            filtered.forEach(itemData => {
                const item = document.createElement('div');
                item.className = 'projectSuggestionItem';
                item.textContent = `${itemData.label} (${itemData.count})`;
                item.setAttribute('data-workspace-name', itemData.label);
                workspaceSuggestions.appendChild(item);
            });

            activeWorkspaceSuggestionIndex = -1;
            workspaceSuggestions.classList.add('visible');
        }

        function renderRequestedBySuggestions(query = '') {
            if (!requestedBySuggestions || !requestedByFilterInput) {
                return;
            }

            const counts = getRequestedByCounts();
            const options = Array.from(counts.entries())
                .map(([id, value]) => ({
                    id,
                    label: value.label,
                    count: value.count,
                }))
                .sort((a, b) => a.label.localeCompare(b.label));
            const normalizedQuery = normalizeRequestedByLabel(query);
            const alreadySelected = new Set(filtersState.selectedRequestedBy.map(value => normalizeRequestedBy(
                value)));

            const filtered = options.filter(option => {
                const normalizedId = normalizeRequestedBy(option.id);
                const normalizedLabel = normalizeRequestedByLabel(option.label);

                if (alreadySelected.has(normalizedId)) {
                    return false;
                }

                if (!normalizedQuery) {
                    return true;
                }

                return normalizedLabel.includes(normalizedQuery) || normalizedId.includes(
                    normalizedQuery);
            }).slice(0, 12);

            requestedBySuggestions.innerHTML = '';

            if (!filtered.length) {
                activeRequestedBySuggestionIndex = -1;
                renderNoMatchSuggestion(requestedBySuggestions);
                return;
            }

            filtered.forEach(option => {
                const item = document.createElement('div');
                item.className = 'projectSuggestionItem';
                item.textContent = `${option.label} (${option.count})`;
                item.setAttribute('data-requested-by-id', option.id);
                item.setAttribute('data-requested-by-label', option.label);
                requestedBySuggestions.appendChild(item);
            });

            activeRequestedBySuggestionIndex = -1;
            requestedBySuggestions.classList.add('visible');
        }

        function renderAssignedToSuggestions(query = '') {
            if (!assignedToSuggestions || !assignedToFilterInput) {
                return;
            }

            const counts = getAssignedToCounts();
            const options = Array.from(counts.entries())
                .map(([id, value]) => ({
                    id,
                    label: value.label,
                    count: value.count,
                }))
                .sort((a, b) => a.label.localeCompare(b.label));
            const normalizedQuery = normalizeRequestedByLabel(query);
            const alreadySelected = new Set(filtersState.selectedAssignedTo.map(value => normalizeRequestedBy(
                value)));

            const filtered = options.filter(option => {
                const normalizedId = normalizeRequestedBy(option.id);
                const normalizedLabel = normalizeRequestedByLabel(option.label);

                if (alreadySelected.has(normalizedId)) {
                    return false;
                }

                if (!normalizedQuery) {
                    return true;
                }

                return normalizedLabel.includes(normalizedQuery) || normalizedId.includes(
                    normalizedQuery);
            }).slice(0, 12);

            assignedToSuggestions.innerHTML = '';

            if (!filtered.length) {
                activeAssignedToSuggestionIndex = -1;
                renderNoMatchSuggestion(assignedToSuggestions);
                return;
            }

            filtered.forEach(option => {
                const item = document.createElement('div');
                item.className = 'projectSuggestionItem';
                item.textContent = `${option.label} (${option.count})`;
                item.setAttribute('data-assigned-to-id', option.id);
                item.setAttribute('data-assigned-to-label', option.label);
                assignedToSuggestions.appendChild(item);
            });

            activeAssignedToSuggestionIndex = -1;
            assignedToSuggestions.classList.add('visible');
        }

        function renderSelectedProjectTags() {
            if (!selectedProjectList) {
                return;
            }

            selectedProjectList.innerHTML = '';

            filtersState.selectedProjects.forEach(projectName => {
                const tag = document.createElement('span');
                tag.className = 'selectedProjectTag';

                const tagLabel = document.createElement('span');
                tagLabel.textContent = projectName;

                const removeButton = document.createElement('button');
                removeButton.type = 'button';
                removeButton.setAttribute('data-project-name', projectName);
                removeButton.textContent = '×';

                tag.appendChild(tagLabel);
                tag.appendChild(removeButton);
                selectedProjectList.appendChild(tag);
            });
        }

        function renderSelectedWorkspaceTags() {
            if (!selectedWorkspaceList) {
                return;
            }

            selectedWorkspaceList.innerHTML = '';

            filtersState.selectedWorkspaces.forEach(workspaceName => {
                const tag = document.createElement('span');
                tag.className = 'selectedProjectTag';

                const tagLabel = document.createElement('span');
                tagLabel.textContent = workspaceName;

                const removeButton = document.createElement('button');
                removeButton.type = 'button';
                removeButton.setAttribute('data-workspace-name', workspaceName);
                removeButton.textContent = '×';

                tag.appendChild(tagLabel);
                tag.appendChild(removeButton);
                selectedWorkspaceList.appendChild(tag);
            });
        }

        function getRequestedByLabel(requestedById) {
            const normalizedId = normalizeRequestedBy(requestedById);
            const options = getAllRequestedByOptions();
            const matchedOption = options.find(option => normalizeRequestedBy(option.id) === normalizedId);
            return matchedOption ? matchedOption.label : normalizedId;
        }

        function renderSelectedRequestedByTags() {
            if (!selectedRequestedByList) {
                return;
            }

            selectedRequestedByList.innerHTML = '';

            filtersState.selectedRequestedBy.forEach(requestedById => {
                const normalizedId = normalizeRequestedBy(requestedById);
                const label = getRequestedByLabel(normalizedId);
                const tag = document.createElement('span');
                tag.className = 'selectedProjectTag';

                const tagLabel = document.createElement('span');
                tagLabel.textContent = label;

                const removeButton = document.createElement('button');
                removeButton.type = 'button';
                removeButton.setAttribute('data-requested-by-id', normalizedId);
                removeButton.textContent = '×';

                tag.appendChild(tagLabel);
                tag.appendChild(removeButton);
                selectedRequestedByList.appendChild(tag);
            });
        }

        function getAssignedToLabel(assignedToId) {
            const normalizedId = normalizeRequestedBy(assignedToId);
            const options = getAllAssignedToOptions();
            const matchedOption = options.find(option => normalizeRequestedBy(option.id) === normalizedId);
            return matchedOption ? matchedOption.label : normalizedId;
        }

        function renderSelectedAssignedToTags() {
            if (!selectedAssignedToList) {
                return;
            }

            selectedAssignedToList.innerHTML = '';

            filtersState.selectedAssignedTo.forEach(assignedToId => {
                const normalizedId = normalizeRequestedBy(assignedToId);
                const label = getAssignedToLabel(normalizedId);
                const tag = document.createElement('span');
                tag.className = 'selectedProjectTag';

                const tagLabel = document.createElement('span');
                tagLabel.textContent = label;

                const removeButton = document.createElement('button');
                removeButton.type = 'button';
                removeButton.setAttribute('data-assigned-to-id', normalizedId);
                removeButton.textContent = '×';

                tag.appendChild(tagLabel);
                tag.appendChild(removeButton);
                selectedAssignedToList.appendChild(tag);
            });

            if (filtersState.selectedAssignedToNone) {
                const tag = document.createElement('span');
                tag.className = 'selectedProjectTag';

                const tagLabel = document.createElement('span');
                tagLabel.textContent = "{{ __('None') }}";

                const removeButton = document.createElement('button');
                removeButton.type = 'button';
                removeButton.setAttribute('data-assigned-to-none', '1');
                removeButton.textContent = '×';

                tag.appendChild(tagLabel);
                tag.appendChild(removeButton);
                selectedAssignedToList.appendChild(tag);
            }
        }

        function addProjectNameFilter() {
            if (!projectFilterInput) {
                return;
            }

            const rawValue = projectFilterInput.value || '';
            const value = rawValue.trim();
            if (!value) {
                return;
            }

            const names = getAllProjectNames();
            const normalizedValue = normalizeProjectName(value);
            const matchedName = names.find(name => normalizeProjectName(name) === normalizedValue);

            if (!matchedName) {
                return;
            }

            if (!filtersState.selectedProjects.includes(matchedName)) {
                filtersState.selectedProjects.push(matchedName);
            }

            projectFilterInput.value = '';
            renderSelectedProjectTags();
            hideProjectSuggestions();
            applyMilestoneFilters();
        }

        function addWorkspaceFilter() {
            if (!workspaceFilterInput) {
                return;
            }

            const rawValue = workspaceFilterInput.value || '';
            const value = rawValue.trim();
            if (!value) {
                return;
            }

            const names = getAllWorkspaceNames();
            const normalizedValue = normalizeWorkspaceName(value);
            const matchedName = names.find(name => normalizeWorkspaceName(name) === normalizedValue);

            if (!matchedName) {
                return;
            }

            if (!filtersState.selectedWorkspaces.includes(matchedName)) {
                filtersState.selectedWorkspaces.push(matchedName);
            }

            workspaceFilterInput.value = '';
            renderSelectedWorkspaceTags();
            hideWorkspaceSuggestions();
            applyMilestoneFilters();
        }

        function addRequestedByFilter() {
            if (!requestedByFilterInput) {
                return;
            }

            const rawValue = requestedByFilterInput.value || '';
            const value = rawValue.trim();
            if (!value) {
                return;
            }

            const options = getAllRequestedByOptions();
            const normalizedValue = normalizeRequestedByLabel(value);
            const matchedOption = options.find(option => {
                const normalizedId = normalizeRequestedBy(option.id);
                const normalizedLabel = normalizeRequestedByLabel(option.label);
                return normalizedLabel === normalizedValue || normalizedId === value;
            });

            if (!matchedOption) {
                return;
            }

            const requestedById = normalizeRequestedBy(matchedOption.id);

            if (!filtersState.selectedRequestedBy.includes(requestedById)) {
                filtersState.selectedRequestedBy.push(requestedById);
            }

            requestedByFilterInput.value = '';
            renderSelectedRequestedByTags();
            hideRequestedBySuggestions();
            applyMilestoneFilters();
        }

        function addAssignedToFilter() {
            if (!assignedToFilterInput) {
                return;
            }

            const rawValue = assignedToFilterInput.value || '';
            const value = rawValue.trim();
            if (!value) {
                return;
            }

            const options = getAllAssignedToOptions();
            const normalizedValue = normalizeRequestedByLabel(value);
            const matchedOption = options.find(option => {
                const normalizedId = normalizeRequestedBy(option.id);
                const normalizedLabel = normalizeRequestedByLabel(option.label);
                return normalizedLabel === normalizedValue || normalizedId === value;
            });

            if (!matchedOption) {
                return;
            }

            const assignedToId = normalizeRequestedBy(matchedOption.id);

            if (!filtersState.selectedAssignedTo.includes(assignedToId)) {
                filtersState.selectedAssignedTo.push(assignedToId);
            }

            assignedToFilterInput.value = '';
            renderSelectedAssignedToTags();
            hideAssignedToSuggestions();
            applyMilestoneFilters();
        }

        function isMine(card) {
            const assignedUser = card.dataset.assignTo;
            const createdBy = card.dataset.createdBy;
            const requestedBy = card.dataset.requestedBy;
            const hasMyTasks = card.dataset.hasMyTasks === '1';
            const isUnassigned = card.classList.contains('notAsignedMilestone');

            return assignedUser == currentUserId ||
                createdBy == currentUserId ||
                requestedBy == currentUserId ||
                hasMyTasks ||
                (isUnassigned && createdBy == currentUserId);
        }

        function hasAnyFilterApplied() {
            return filtersState.hideUnassigned ||
                (hasShowAllFilter && !filtersState.showAll) ||
                filtersState.selectedPriorities.length > 0 ||
                filtersState.selectedProjectTypes.length > 0 ||
                filtersState.selectedProjects.length > 0 ||
                (isMyBoardMode && filtersState.selectedWorkspaces.length > 0) ||
                filtersState.selectedRequestedBy.length > 0 ||
                filtersState.selectedAssignedTo.length > 0 ||
                filtersState.selectedAssignedToNone ||
                (filtersState.dateField && (filtersState.dateFrom || filtersState.dateTo)) ||
                (hasCompletedFilter && filtersState.showCompleted);
        }

        function updateResetFiltersButtonVisibility() {
            if (!resetFiltersBtn && !applyFiltersBtn) {
                return;
            }

            const hasFiltersApplied = hasAnyFilterApplied();

            if (resetFiltersBtn) {
                resetFiltersBtn.classList.toggle('d-none', !hasFiltersApplied);
            }

            if (applyFiltersBtn) {
                applyFiltersBtn.classList.toggle('d-none', !hasFiltersApplied);
            }
        }

        function resetAllMilestoneFilters() {
            filtersState.hideUnassigned = false;
            filtersState.showAll = true;
            filtersState.showCompleted = hasCompletedFilter ? false : true;
            filtersState.selectedPriorities = [];
            filtersState.selectedProjectTypes = [];
            filtersState.selectedProjects = [];
            filtersState.selectedWorkspaces = [];
            filtersState.selectedRequestedBy = [];
            filtersState.selectedAssignedTo = [];
            filtersState.selectedAssignedToNone = false;
            filtersState.dateField = 'desired_delivery';
            filtersState.dateFrom = '';
            filtersState.dateTo = '';

            window.milestoneBoardShowCompleted = filtersState.showCompleted;

            if (projectFilterInput) {
                projectFilterInput.value = '';
            }

            if (requestedByFilterInput) {
                requestedByFilterInput.value = '';
            }

            if (workspaceFilterInput) {
                workspaceFilterInput.value = '';
            }

            if (assignedToFilterInput) {
                assignedToFilterInput.value = '';
            }

            if (assignedToNoneCheckbox) {
                assignedToNoneCheckbox.checked = false;
            }

            if (dateRangeField) {
                dateRangeField.value = filtersState.dateField;
            }

            if (dateRangeFrom) {
                dateRangeFrom.value = '';
            }

            if (dateRangeTo) {
                dateRangeTo.value = '';
            }

            hideProjectSuggestions();
            hideWorkspaceSuggestions();
            hideRequestedBySuggestions();
            hideAssignedToSuggestions();
            renderSelectedProjectTags();
            renderSelectedWorkspaceTags();
            renderSelectedRequestedByTags();
            renderSelectedAssignedToTags();

            if (priorityFilterCheckboxes.length) {
                priorityFilterCheckboxes.forEach(checkbox => {
                    checkbox.checked = false;
                });
            }

            const projectTypeCheckboxes = Array.from(document.querySelectorAll('.projectTypeFilterCheckbox'));
            projectTypeCheckboxes.forEach(checkbox => {
                checkbox.checked = false;
            });

            renderProjectTypeCheckboxes();

            syncBinaryFiltersUi();

            applyMilestoneFilters();
        }

        function applyMilestoneFilters() {
            const allMilestones = document.querySelectorAll('.card[data-project-id]');
            const groupedByProject = groupMilestonesByProject(allMilestones);
            const criteria = getFilterCriteria();

            allMilestones.forEach(card => {
                const allInStatus4 = isCompletedProject(card, groupedByProject);
                const visible = cardMatchesFilters(card, groupedByProject, criteria);

                card.style.display = visible ? '' : 'none';

                if (hasCompletedFilter) {
                    card.style.border = (filtersState.showCompleted && allInStatus4) ?
                        '3px solid #15b500' : 'none';
                }
            });

            document.querySelectorAll('.kanban-box.fixedHeight').forEach(column => {
                const cards = column.querySelectorAll('.card[data-project-id]');
                const hiddenMsg = column.querySelector('.filtered-empty-state');
                if (!hiddenMsg) return;
                const visibleCount = Array.from(cards).filter(c => c.style.display !== 'none').length;
                hiddenMsg.style.display = (cards.length > 0 && visibleCount === 0) ? 'flex' : 'none';
            });

            updatePriorityOptionCounts();
            renderProjectTypeCheckboxes();
            renderActiveFiltersChips();

            updateResetFiltersButtonVisibility();
        }

        if (hasCompletedFilter) {
            [showCompletedProjectsYes, showCompletedProjectsNo].forEach(radio => {
                radio.addEventListener('change', function() {
                    filtersState.showCompleted = showCompletedProjectsYes.checked;
                    window.milestoneBoardShowCompleted = filtersState.showCompleted;
                    applyMilestoneFilters();
                });
            });
        }

        if (dateRangeField) {
            dateRangeField.value = filtersState.dateField;

            dateRangeField.addEventListener('change', function() {
                filtersState.dateField = this.value || 'desired_delivery';
                applyMilestoneFilters();
            });
        }

        if (dateRangeFrom) {
            dateRangeFrom.value = filtersState.dateFrom;

            dateRangeFrom.addEventListener('change', function() {
                filtersState.dateFrom = normalizeDateValue(this.value);
                applyMilestoneFilters();
            });
        }

        if (dateRangeTo) {
            dateRangeTo.value = filtersState.dateTo;

            dateRangeTo.addEventListener('change', function() {
                filtersState.dateTo = normalizeDateValue(this.value);
                applyMilestoneFilters();
            });
        }

        if (applyProjectFilterBtn) {
            applyProjectFilterBtn.addEventListener('click', function() {
                addProjectNameFilter();
            });
        }

        if (applyWorkspaceFilterBtn) {
            applyWorkspaceFilterBtn.addEventListener('click', function() {
                addWorkspaceFilter();
            });
        }

        if (applyRequestedByFilterBtn) {
            applyRequestedByFilterBtn.addEventListener('click', function() {
                addRequestedByFilter();
            });
        }

        if (applyAssignedToFilterBtn) {
            applyAssignedToFilterBtn.addEventListener('click', function() {
                addAssignedToFilter();
            });
        }

        if (assignedToNoneCheckbox) {
            assignedToNoneCheckbox.addEventListener('change', function() {
                filtersState.selectedAssignedToNone = !!this.checked;

                if (filtersState.selectedAssignedToNone && filtersState.hideUnassigned) {
                    filtersState.hideUnassigned = false;
                    syncBinaryFiltersUi();
                }

                renderSelectedAssignedToTags();
                applyMilestoneFilters();
            });
        }

        if (priorityFilterCheckboxes.length) {
            priorityFilterCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    filtersState.selectedPriorities = priorityFilterCheckboxes
                        .filter(item => item.checked)
                        .map(item => item.value);
                    applyMilestoneFilters();
                });
            });
        }

        if (projectTypeCheckboxList) {
            projectTypeCheckboxList.addEventListener('change', function(event) {
                const checkbox = event.target.closest('.projectTypeFilterCheckbox');
                if (!checkbox) {
                    return;
                }

                filtersState.selectedProjectTypes = Array.from(document.querySelectorAll(
                        '.projectTypeFilterCheckbox'))
                    .filter(item => item.checked)
                    .map(item => normalizeProjectType(item.value));
                applyMilestoneFilters();
            });
        }

        if (projectFilterInput) {
            projectFilterInput.addEventListener('input', function() {
                renderProjectSuggestions(this.value || '');
            });

            projectFilterInput.addEventListener('focus', function() {
                renderProjectSuggestions(this.value || '');
            });

            projectFilterInput.addEventListener('keydown', function(event) {
                if (event.key === 'ArrowDown') {
                    event.preventDefault();
                    if (!projectSuggestions || !projectSuggestions.classList.contains('visible')) {
                        renderProjectSuggestions(this.value || '');
                    }
                    setActiveSuggestion(activeSuggestionIndex + 1);
                    return;
                }

                if (event.key === 'ArrowUp') {
                    event.preventDefault();
                    if (!projectSuggestions || !projectSuggestions.classList.contains('visible')) {
                        renderProjectSuggestions(this.value || '');
                    }
                    setActiveSuggestion(activeSuggestionIndex - 1);
                    return;
                }

                if (event.key === 'Enter') {
                    event.preventDefault();

                    const items = getSuggestionItems();
                    if (projectSuggestions && projectSuggestions.classList.contains('visible') &&
                        activeSuggestionIndex >= 0 && items[activeSuggestionIndex]) {
                        this.value = items[activeSuggestionIndex].getAttribute('data-project-name') ||
                            this.value;
                    }

                    addProjectNameFilter();
                    return;
                }

                if (event.key === 'Escape') {
                    hideProjectSuggestions();
                }
            });
        }

        if (workspaceFilterInput) {
            workspaceFilterInput.addEventListener('input', function() {
                renderWorkspaceSuggestions(this.value || '');
            });

            workspaceFilterInput.addEventListener('focus', function() {
                renderWorkspaceSuggestions(this.value || '');
            });

            workspaceFilterInput.addEventListener('keydown', function(event) {
                if (event.key === 'ArrowDown') {
                    event.preventDefault();
                    if (!workspaceSuggestions || !workspaceSuggestions.classList.contains('visible')) {
                        renderWorkspaceSuggestions(this.value || '');
                    }
                    setActiveWorkspaceSuggestion(activeWorkspaceSuggestionIndex + 1);
                    return;
                }

                if (event.key === 'ArrowUp') {
                    event.preventDefault();
                    if (!workspaceSuggestions || !workspaceSuggestions.classList.contains('visible')) {
                        renderWorkspaceSuggestions(this.value || '');
                    }
                    setActiveWorkspaceSuggestion(activeWorkspaceSuggestionIndex - 1);
                    return;
                }

                if (event.key === 'Enter') {
                    event.preventDefault();

                    const items = getWorkspaceSuggestionItems();
                    if (workspaceSuggestions && workspaceSuggestions.classList.contains('visible') &&
                        activeWorkspaceSuggestionIndex >= 0 && items[activeWorkspaceSuggestionIndex]) {
                        this.value = items[activeWorkspaceSuggestionIndex].getAttribute(
                                'data-workspace-name') ||
                            this.value;
                    }

                    addWorkspaceFilter();
                    return;
                }

                if (event.key === 'Escape') {
                    hideWorkspaceSuggestions();
                }
            });
        }

        if (requestedByFilterInput) {
            requestedByFilterInput.addEventListener('input', function() {
                renderRequestedBySuggestions(this.value || '');
            });

            requestedByFilterInput.addEventListener('focus', function() {
                renderRequestedBySuggestions(this.value || '');
            });

            requestedByFilterInput.addEventListener('keydown', function(event) {
                if (event.key === 'ArrowDown') {
                    event.preventDefault();
                    if (!requestedBySuggestions || !requestedBySuggestions.classList.contains(
                            'visible')) {
                        renderRequestedBySuggestions(this.value || '');
                    }
                    setActiveRequestedBySuggestion(activeRequestedBySuggestionIndex + 1);
                    return;
                }

                if (event.key === 'ArrowUp') {
                    event.preventDefault();
                    if (!requestedBySuggestions || !requestedBySuggestions.classList.contains(
                            'visible')) {
                        renderRequestedBySuggestions(this.value || '');
                    }
                    setActiveRequestedBySuggestion(activeRequestedBySuggestionIndex - 1);
                    return;
                }

                if (event.key === 'Enter') {
                    event.preventDefault();

                    const items = getRequestedBySuggestionItems();
                    if (requestedBySuggestions && requestedBySuggestions.classList.contains(
                            'visible') &&
                        activeRequestedBySuggestionIndex >= 0 && items[activeRequestedBySuggestionIndex]
                    ) {
                        this.value = items[activeRequestedBySuggestionIndex].getAttribute(
                                'data-requested-by-label') ||
                            this.value;
                    }

                    addRequestedByFilter();
                    return;
                }

                if (event.key === 'Escape') {
                    hideRequestedBySuggestions();
                }
            });
        }

        if (assignedToFilterInput) {
            assignedToFilterInput.addEventListener('input', function() {
                renderAssignedToSuggestions(this.value || '');
            });

            assignedToFilterInput.addEventListener('focus', function() {
                renderAssignedToSuggestions(this.value || '');
            });

            assignedToFilterInput.addEventListener('keydown', function(event) {
                if (event.key === 'ArrowDown') {
                    event.preventDefault();
                    if (!assignedToSuggestions || !assignedToSuggestions.classList.contains(
                            'visible')) {
                        renderAssignedToSuggestions(this.value || '');
                    }
                    setActiveAssignedToSuggestion(activeAssignedToSuggestionIndex + 1);
                    return;
                }

                if (event.key === 'ArrowUp') {
                    event.preventDefault();
                    if (!assignedToSuggestions || !assignedToSuggestions.classList.contains(
                            'visible')) {
                        renderAssignedToSuggestions(this.value || '');
                    }
                    setActiveAssignedToSuggestion(activeAssignedToSuggestionIndex - 1);
                    return;
                }

                if (event.key === 'Enter') {
                    event.preventDefault();

                    const items = getAssignedToSuggestionItems();
                    if (assignedToSuggestions && assignedToSuggestions.classList.contains('visible') &&
                        activeAssignedToSuggestionIndex >= 0 && items[activeAssignedToSuggestionIndex]
                    ) {
                        this.value = items[activeAssignedToSuggestionIndex].getAttribute(
                                'data-assigned-to-label') ||
                            this.value;
                    }

                    addAssignedToFilter();
                    return;
                }

                if (event.key === 'Escape') {
                    hideAssignedToSuggestions();
                }
            });
        }

        if (projectSuggestions) {
            projectSuggestions.addEventListener('click', function(event) {
                const item = event.target.closest('.projectSuggestionItem');
                if (!item || !projectFilterInput || !item.getAttribute('data-project-name')) {
                    return;
                }

                projectFilterInput.value = item.getAttribute('data-project-name') || '';
                hideProjectSuggestions();
                projectFilterInput.focus();
            });

            projectSuggestions.addEventListener('mousemove', function(event) {
                const item = event.target.closest('.projectSuggestionItem');
                if (!item) {
                    return;
                }

                const items = getSuggestionItems();
                const index = items.indexOf(item);
                if (index !== -1 && index !== activeSuggestionIndex) {
                    setActiveSuggestion(index);
                }
            });
        }

        if (workspaceSuggestions) {
            workspaceSuggestions.addEventListener('click', function(event) {
                const item = event.target.closest('.projectSuggestionItem');
                if (!item || !workspaceFilterInput || !item.getAttribute('data-workspace-name')) {
                    return;
                }

                workspaceFilterInput.value = item.getAttribute('data-workspace-name') || '';
                hideWorkspaceSuggestions();
                workspaceFilterInput.focus();
            });

            workspaceSuggestions.addEventListener('mousemove', function(event) {
                const item = event.target.closest('.projectSuggestionItem');
                if (!item) {
                    return;
                }

                const items = getWorkspaceSuggestionItems();
                const index = items.indexOf(item);
                if (index !== -1 && index !== activeWorkspaceSuggestionIndex) {
                    setActiveWorkspaceSuggestion(index);
                }
            });
        }

        if (requestedBySuggestions) {
            requestedBySuggestions.addEventListener('click', function(event) {
                const item = event.target.closest('.projectSuggestionItem');
                if (!item || !requestedByFilterInput || !item.getAttribute('data-requested-by-label')) {
                    return;
                }

                requestedByFilterInput.value = item.getAttribute('data-requested-by-label') || '';
                hideRequestedBySuggestions();
                requestedByFilterInput.focus();
            });

            requestedBySuggestions.addEventListener('mousemove', function(event) {
                const item = event.target.closest('.projectSuggestionItem');
                if (!item) {
                    return;
                }

                const items = getRequestedBySuggestionItems();
                const index = items.indexOf(item);
                if (index !== -1 && index !== activeRequestedBySuggestionIndex) {
                    setActiveRequestedBySuggestion(index);
                }
            });
        }

        if (assignedToSuggestions) {
            assignedToSuggestions.addEventListener('click', function(event) {
                const item = event.target.closest('.projectSuggestionItem');
                if (!item || !assignedToFilterInput || !item.getAttribute('data-assigned-to-label')) {
                    return;
                }

                assignedToFilterInput.value = item.getAttribute('data-assigned-to-label') || '';
                hideAssignedToSuggestions();
                assignedToFilterInput.focus();
            });

            assignedToSuggestions.addEventListener('mousemove', function(event) {
                const item = event.target.closest('.projectSuggestionItem');
                if (!item) {
                    return;
                }

                const items = getAssignedToSuggestionItems();
                const index = items.indexOf(item);
                if (index !== -1 && index !== activeAssignedToSuggestionIndex) {
                    setActiveAssignedToSuggestion(index);
                }
            });
        }

        document.addEventListener('click', function(event) {
            if (projectSuggestions && projectFilterInput) {
                const insideProjectInput = event.target.closest(
                    '#projectFilterContent .projectFilterAutocomplete');
                if (!insideProjectInput) {
                    hideProjectSuggestions();
                }
            }

            if (workspaceSuggestions && workspaceFilterInput) {
                const insideWorkspaceInput = event.target.closest(
                    '#workspaceFilterContent .projectFilterAutocomplete');
                if (!insideWorkspaceInput) {
                    hideWorkspaceSuggestions();
                }
            }

            if (requestedBySuggestions && requestedByFilterInput) {
                const insideRequestedInput = event.target.closest(
                    '#requestedByFilterContent .projectFilterAutocomplete');
                if (!insideRequestedInput) {
                    hideRequestedBySuggestions();
                }
            }

            if (assignedToSuggestions && assignedToFilterInput) {
                const insideAssignedInput = event.target.closest(
                    '#assignedToFilterContent .projectFilterAutocomplete');
                if (!insideAssignedInput) {
                    hideAssignedToSuggestions();
                }
            }
        });

        if (selectedProjectList) {
            selectedProjectList.addEventListener('click', function(event) {
                const removeButton = event.target.closest('button[data-project-name]');
                if (!removeButton) {
                    return;
                }

                const nameToRemove = removeButton.getAttribute('data-project-name');
                filtersState.selectedProjects = filtersState.selectedProjects.filter(name => name !==
                    nameToRemove);
                renderSelectedProjectTags();
                applyMilestoneFilters();
            });
        }

        if (selectedWorkspaceList) {
            selectedWorkspaceList.addEventListener('click', function(event) {
                const removeButton = event.target.closest('button[data-workspace-name]');
                if (!removeButton) {
                    return;
                }

                const nameToRemove = removeButton.getAttribute('data-workspace-name');
                filtersState.selectedWorkspaces = filtersState.selectedWorkspaces.filter(name =>
                    name !==
                    nameToRemove);
                renderSelectedWorkspaceTags();
                applyMilestoneFilters();
            });
        }

        if (selectedRequestedByList) {
            selectedRequestedByList.addEventListener('click', function(event) {
                const removeButton = event.target.closest('button[data-requested-by-id]');
                if (!removeButton) {
                    return;
                }

                const requestedByIdToRemove = normalizeRequestedBy(removeButton.getAttribute(
                    'data-requested-by-id'));
                filtersState.selectedRequestedBy = filtersState.selectedRequestedBy.filter(value =>
                    normalizeRequestedBy(value) !== requestedByIdToRemove);
                renderSelectedRequestedByTags();
                applyMilestoneFilters();
            });
        }

        if (selectedAssignedToList) {
            selectedAssignedToList.addEventListener('click', function(event) {
                const removeNoneButton = event.target.closest('button[data-assigned-to-none]');
                if (removeNoneButton) {
                    filtersState.selectedAssignedToNone = false;
                    if (assignedToNoneCheckbox) {
                        assignedToNoneCheckbox.checked = false;
                    }
                    renderSelectedAssignedToTags();
                    applyMilestoneFilters();
                    return;
                }

                const removeButton = event.target.closest('button[data-assigned-to-id]');
                if (!removeButton) {
                    return;
                }

                const assignedToIdToRemove = normalizeRequestedBy(removeButton.getAttribute(
                    'data-assigned-to-id'));
                filtersState.selectedAssignedTo = filtersState.selectedAssignedTo.filter(value =>
                    normalizeRequestedBy(value) !== assignedToIdToRemove);
                renderSelectedAssignedToTags();
                applyMilestoneFilters();
            });
        }

        if (hideUnassignedYes && hideUnassignedNo) {
            [hideUnassignedYes, hideUnassignedNo].forEach(radio => {
                radio.addEventListener('change', function() {
                    filtersState.hideUnassigned = hideUnassignedYes.checked;
                    applyMilestoneFilters();
                });
            });
        }

        if (hasShowAllFilter) {
            [showAllMilestonesYes, showAllMilestonesNo].forEach(radio => {
                radio.addEventListener('change', function() {
                    filtersState.showAll = showAllMilestonesYes.checked;
                    applyMilestoneFilters();
                });
            });
        }

        if (resetFiltersBtn) {
            resetFiltersBtn.addEventListener('click', function() {
                resetAllMilestoneFilters();
            });
        }

        if (activeFiltersList) {
            activeFiltersList.addEventListener('click', function(event) {
                const removeButton = event.target.closest('button[data-chip-type]');
                if (!removeButton) {
                    return;
                }

                const chipType = removeButton.getAttribute('data-chip-type');
                const chipValue = removeButton.getAttribute('data-chip-value') || '';

                if (chipType === 'hideUnassigned') {
                    filtersState.hideUnassigned = false;
                    syncBinaryFiltersUi();
                } else if (chipType === 'showAll') {
                    filtersState.showAll = true;
                    syncBinaryFiltersUi();
                } else if (chipType === 'showCompleted') {
                    filtersState.showCompleted = false;
                    syncBinaryFiltersUi();
                    window.milestoneBoardShowCompleted = filtersState.showCompleted;
                } else if (chipType === 'priority') {
                    filtersState.selectedPriorities = filtersState.selectedPriorities.filter(value =>
                        value !== chipValue);
                    const target = document.querySelector(
                        `.priorityFilterCheckbox[value="${chipValue}"]`);
                    if (target) {
                        target.checked = false;
                    }
                } else if (chipType === 'projectType') {
                    const normalized = normalizeProjectType(chipValue);
                    filtersState.selectedProjectTypes = filtersState.selectedProjectTypes.filter(
                        value =>
                        normalizeProjectType(value) !== normalized);
                    const target = document.querySelector(
                        `.projectTypeFilterCheckbox[value="${normalized}"]`);
                    if (target) {
                        target.checked = false;
                    }
                } else if (chipType === 'project') {
                    filtersState.selectedProjects = filtersState.selectedProjects.filter(value =>
                        value !== chipValue);
                    renderSelectedProjectTags();
                } else if (chipType === 'workspace') {
                    filtersState.selectedWorkspaces = filtersState.selectedWorkspaces.filter(value =>
                        value !== chipValue);
                    renderSelectedWorkspaceTags();
                } else if (chipType === 'requestedBy') {
                    const normalized = normalizeRequestedBy(chipValue);
                    filtersState.selectedRequestedBy = filtersState.selectedRequestedBy.filter(value =>
                        normalizeRequestedBy(value) !== normalized);
                    renderSelectedRequestedByTags();
                } else if (chipType === 'assignedTo') {
                    const normalized = normalizeRequestedBy(chipValue);
                    filtersState.selectedAssignedTo = filtersState.selectedAssignedTo.filter(value =>
                        normalizeRequestedBy(value) !== normalized);
                    renderSelectedAssignedToTags();
                } else if (chipType === 'assignedToNone') {
                    filtersState.selectedAssignedToNone = false;
                    if (assignedToNoneCheckbox) {
                        assignedToNoneCheckbox.checked = false;
                    }
                    renderSelectedAssignedToTags();
                } else if (chipType === 'dateRange') {
                    filtersState.dateFrom = '';
                    filtersState.dateTo = '';
                    if (dateRangeFrom) {
                        dateRangeFrom.value = '';
                    }
                    if (dateRangeTo) {
                        dateRangeTo.value = '';
                    }
                }

                applyMilestoneFilters();
            });
        }

        const milestoneCards = document.querySelectorAll('.card[data-project-id]');
        milestoneCards.forEach(card => {
            const observer = new MutationObserver(mutations => {
                mutations.forEach(mutation => {
                    if (mutation.type === 'attributes' && mutation.attributeName ===
                        'data-status') {
                        renderProjectSuggestions(projectFilterInput ? projectFilterInput
                            .value : '');
                        renderWorkspaceSuggestions(workspaceFilterInput ?
                            workspaceFilterInput
                            .value : '');
                        renderRequestedBySuggestions(requestedByFilterInput ?
                            requestedByFilterInput.value : '');
                        renderAssignedToSuggestions(assignedToFilterInput ?
                            assignedToFilterInput.value : '');
                        renderProjectTypeCheckboxes();
                        applyMilestoneFilters();
                    }
                });
            });
            observer.observe(card, {
                attributes: true,
                attributeFilter: ['data-status']
            });
        });

        renderProjectSuggestions('');
        renderWorkspaceSuggestions('');
        renderRequestedBySuggestions('');
        renderAssignedToSuggestions('');
        renderProjectTypeCheckboxes();
        renderSelectedProjectTags();
        renderSelectedWorkspaceTags();
        renderSelectedRequestedByTags();
        renderSelectedAssignedToTags();
        if (assignedToNoneCheckbox) {
            assignedToNoneCheckbox.checked = !!filtersState.selectedAssignedToNone;
        }
        syncBinaryFiltersUi();
        if (priorityFilterCheckboxes.length) {
            priorityFilterCheckboxes.forEach(function(cb) {
                cb.checked = filtersState.selectedPriorities.includes(cb.value);
            });
        }
        applyMilestoneFilters();
    });
</script>
