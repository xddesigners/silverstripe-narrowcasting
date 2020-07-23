<% if $Slides %>
    <div class="reveal">
        <div class="slides">
            <% loop $Slides %>
                <section $SlideConfigAttributes class="slide $BemClassName" id="$Anchor">
                    $Me
                </section>
            <% end_loop %>
        </div>
    </div>
<% end_if %>
