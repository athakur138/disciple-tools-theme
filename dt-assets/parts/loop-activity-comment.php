<?php
( function() {
    ?>

    <div class="grid-y">
        <div class="cell grid-x grid-margin-x" id="add-comment-section">
            <div class="auto cell">
            <textarea class="mention" dir="auto"  id="comment-input"
                      placeholder="<?php esc_html_e( "Write your comment or note here", 'disciple_tools' ) ?>"
            ></textarea>
            </div>
        </div>
        <div class="cell grid-x" style="margin-bottom: 20px">
            <div class="cell auto">
                <?php if ( is_singular( "contacts" ) ) :
                    $contact = Disciple_Tools_Contacts::get_contact( get_the_ID(), true, true );
                    $contact_fields = Disciple_Tools_Contacts::get_contact_fields();
                    ?>

                    <!-- work out how to display/align this better  -->

                    <button class="help-button" data-section="quick-action-help-text">
                        <img class="help-icon" src="<?php echo esc_html( get_template_directory_uri() . '/dt-assets/images/help.svg' ) ?>"/>
                    </button>


                <ul class="dropdown menu" data-dropdown-menu $dropdownmenu-arrow-color="white">
                    <li style="border-radius: 5px">
                        <a class="button menu-white-dropdown-arrow"
                           style="background-color: #00897B; color: white;">
                            <?php esc_html_e( "Quick actions", 'disciple_tools' ) ?></a>
                        <ul class="menu" style="width: max-content">
                            <?php
                            foreach ( $contact_fields as $field => $val ) {
                                if ( strpos( $field, "quick_button" ) === 0 ) {
                                    $current_value = 0;
                                    if ( isset( $contact[ $field ] ) ) {
                                        $current_value = $contact[ $field ];
                                    } ?>
                                    <li class="quick-action-menu" data-id="<?php echo esc_attr( $field ) ?>">
                                        <a>
                                            <img src="<?php echo esc_url( get_template_directory_uri() . "/dt-assets/images/" . $val['icon'] ); ?>">
                                            <?php echo esc_html( $val["name"] ); ?>
                                            (<span class="<?php echo esc_attr( $field ) ?>"><?php echo esc_html( $current_value ); ?></span>)
                                        </a>
                                    </li>
                                    <?php
                                }
                            }
                            ?>
                        </ul>
                    </li>
                </ul>
                <?php endif; ?>



            </div>
            <div class="shrink cell" id="add-comment-button-container">
                <button id="add-comment-button" class="button loader">
                    <?php esc_html_e( "Submit comment", 'disciple_tools' ) ?>
                </button>
            </div>
        </div>
        <div class="cell">
            <h3 class="section-header" style="display: inline-block"><?php esc_html_e( "Comments and Activity", 'disciple_tools' ) ?></h3>
            <div id="comments-activity-spinner" style="display: inline-block" class="loading-spinner"></div>
            <div>
            <span style="display: inline-block; margin-right:5px; vertical-align:top; font-weight: bold"><?php esc_html_e( "Showing:", 'disciple_tools' ) ?></span>
            <ul id="comment-activity-tabs" style="display: inline-block; margin: 0">

                <?php
                $sections = [
                    [
                        "key" => "comment",
                        "label" => __( "Comments", 'disciple_tools' ),
                        "selected_by_default" => true
                    ],
                    [
                        "key" => "activity",
                        "label" => __( "Activity", 'disciple_tools' ),
                        "selected_by_default" => true
                    ]
                ];
                $post_type = get_post_type();
                $sections = apply_filters( 'dt_comments_additional_sections', $sections, $post_type );
                foreach ( $sections as $section ) :
                    if ( isset( $section["key"] ) && isset( $section["label"] ) ) :
                        $class = ( isset( $section["selected_by_default"] ) && $section["selected_by_default"] === true ) ?
                            "selected-select-button" : "empty-select-button"
                        ?>
                    <li class="tabs-title" >
                        <label for="tab-button-<?php echo esc_html( $section["key"] ) ?>" >
                            <input type="checkbox"
                                   name="<?php echo esc_html( $section["key"] ) ?>"
                                   id="tab-button-<?php echo esc_html( $section["key"] ) ?>"
                                   data-id="<?php echo esc_html( $section["key"] ) ?>"
                                   class="tabs-section"
                                   <?php echo esc_html( ( isset( $section["selected_by_default"] ) && $section["selected_by_default"] === true ) ? 'checked' : '' ) ?>
                            >
                            <span class="tab-button-label" data-id="<?php echo esc_html( $section["key"] ) ?>"> <?php echo esc_html( $section["label"] ) ?></span>
                        </label>

                    </li>
                    <?php endif;
                endforeach; ?>
                <li class="tabs-title">
                    <button id="show-all-tabs" class="show-tabs"><?php esc_html_e( "show all", 'disciple_tools' ) ?></button>
                </li>
                <li class="tabs-title">
                    <button id="hide-all-tabs" class="show-tabs"><?php esc_html_e( "hide all", 'disciple_tools' ) ?></button>
                </li>
            </ul>
            </div>
        </div>

        <div id="comments-wrapper" class="cell tabs-content">

        </div>
    </div>



    <div class="reveal" id="delete-comment-modal" data-reveal>
        <p class="lead"><?php esc_html_e( 'Delete Comment:', 'disciple_tools' )?></p>
        <p id="comment-to-delete"></p>
        <div class="grid-x">
            <button class="button button-cancel clear" data-close aria-label="Close reveal" type="button">
                <?php esc_html_e( 'Close', 'disciple_tools' )?>
            </button>
            <button class="button alert loader" aria-label="confirm" type="button" id="confirm-comment-delete">
                <?php esc_html_e( 'Delete', 'disciple_tools' )?>
            </button>
            <button class="close-button" data-close aria-label="Close modal" type="button">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="delete-comment callout small alert" style="display: none">
            <h5><?php esc_html_e( "Sorry, something went wrong", 'disciple_tools' ) ?></h5>
            <p id="delete-comment-error"><?php esc_html_e( "The comment could not be deleted.", 'disciple_tools' ) ?></p>
        </div>
    </div>

    <div class="reveal" id="edit-comment-modal" data-reveal>
        <p class="lead"><?php esc_html_e( 'Edit Comment:', 'disciple_tools' )?></p>
        <textarea id="comment-to-edit" rows="10" dir="auto"></textarea>
        <div class="grid-x">
            <button class="button button-cancel clear" data-close aria-label="Close reveal" type="button">
                <?php esc_html_e( 'Close', 'disciple_tools' )?>
            </button>
            <button class="button loader" aria-label="confirm" type="button" id="confirm-comment-edit">
                <?php esc_html_e( 'Update', 'disciple_tools' )?>
            </button>
            <button class="close-button" data-close aria-label="Close modal" type="button">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="edit-comment callout small alert" style="display: none">
            <h5><?php esc_html_e( "Sorry, something went wrong", 'disciple_tools' ) ?></h5>
            <p id="edit-comment-error"><?php esc_html_e( "The comment could not be updated.", 'disciple_tools' ) ?></p>
        </div>
    </div>


    <?php
} )();
