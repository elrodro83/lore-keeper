package ar.com.lorekeeper.client.main;

import ar.com.lorekeeper.client.main.LoreKeeperSuggestOracle.LoreKeeperSuggestion;

import com.google.gwt.core.client.GWT;
import com.google.gwt.event.logical.shared.SelectionEvent;
import com.google.gwt.uibinder.client.UiBinder;
import com.google.gwt.uibinder.client.UiField;
import com.google.gwt.uibinder.client.UiHandler;
import com.google.gwt.user.client.Window;
import com.google.gwt.user.client.ui.Composite;
import com.google.gwt.user.client.ui.SuggestBox;
import com.google.gwt.user.client.ui.SuggestOracle.Suggestion;
import com.google.gwt.user.client.ui.Widget;

public class MainWidget extends Composite {

	interface MainWidgetUiBinder extends UiBinder<Widget, MainWidget> {
	}

	private static MainWidgetUiBinder uiBinder = GWT
			.create(MainWidgetUiBinder.class);

	@UiField(provided = true)
	SuggestBox sgbSearch;

	public MainWidget() {
		sgbSearch = new SuggestBox(new LoreKeeperSuggestOracle());
		initWidget(uiBinder.createAndBindUi(this));
	}

	@UiHandler(value = "sgbSearch")
	void onSgbSelection(final SelectionEvent<Suggestion> event) {
		final LoreKeeperSuggestion lkSuggestion = (LoreKeeperSuggestion) event
				.getSelectedItem();
		Window.alert(lkSuggestion.getType() + ": " + lkSuggestion.getId());
	}
}
