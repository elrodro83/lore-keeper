package ar.com.lorekeeper.client.main;

import java.util.ArrayList;
import java.util.List;

import ar.com.lorekeeper.client.LoreKeeperState;
import ar.com.lorekeeper.shared.bean.SearchResultProxy;

import com.google.gwt.user.client.ui.SuggestOracle;
import com.google.web.bindery.requestfactory.shared.Receiver;

public class LoreKeeperSuggestOracle extends SuggestOracle {

	public final class LoreKeeperSuggestion implements Suggestion {
		private final String type;
		private final Long id;
		private final String replacementString;
		private final String displayString;

		public LoreKeeperSuggestion(final SearchResultProxy searchResult) {
			this.type = searchResult.getType();
			this.id = searchResult.getId();
			this.replacementString = searchResult.getName();
			this.displayString = "<b>" + searchResult.getName()
					+ "</b><br/>&nbsp;&nbsp;&nbsp;&nbsp;"
					+ searchResult.getDescription();
		}

		public String getType() {
			return type;
		}

		public Long getId() {
			return id;
		}

		@Override
		public String getReplacementString() {
			return replacementString;
		}

		@Override
		public String getDisplayString() {
			return displayString;
		}
	}

	@Override
	public boolean isDisplayStringHTML() {
		return true;
	}

	@Override
	public void requestSuggestions(final Request request,
			final Callback callback) {
		LoreKeeperState.INSTANCE.rf.searchRequest().search(request.getQuery())
				.fire(new Receiver<List<SearchResultProxy>>() {
					@Override
					public void onSuccess(final List<SearchResultProxy> response) {
						final List<Suggestion> suggestions = new ArrayList<Suggestion>();

						for (final SearchResultProxy searchResult : response) {
							suggestions.add(new LoreKeeperSuggestion(
									searchResult));
						}

						callback.onSuggestionsReady(request, new Response(
								suggestions));
					}
				});
	}

}
