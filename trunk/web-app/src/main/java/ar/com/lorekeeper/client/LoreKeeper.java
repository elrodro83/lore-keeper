package ar.com.lorekeeper.client;

import ar.com.lorekeeper.client.main.MainWidget;
import ar.com.lorekeeper.shared.service.LoreKeeperRequestFactory;

import com.google.gwt.core.client.EntryPoint;
import com.google.gwt.core.client.GWT;
import com.google.gwt.event.shared.EventBus;
import com.google.gwt.event.shared.SimpleEventBus;
import com.google.gwt.user.client.ui.DeckPanel;
import com.google.gwt.user.client.ui.RootPanel;

/**
 * Entry point classes define <code>onModuleLoad()</code>.
 */
public class LoreKeeper implements EntryPoint {

	/**
	 * This is the entry point method.
	 */
	@Override
	public void onModuleLoad() {
		LoreKeeperState.INSTANCE.appPanel = new DeckPanel();
		final MainWidget wMain = new MainWidget();
		LoreKeeperState.INSTANCE.appPanel.add(wMain);
		LoreKeeperState.INSTANCE.appPanel.showWidget(0);

		RootPanel.get("content").add(LoreKeeperState.INSTANCE.appPanel);

		final EventBus eventBus = new SimpleEventBus();
		LoreKeeperState.INSTANCE.rf = GWT
				.create(LoreKeeperRequestFactory.class);
		LoreKeeperState.INSTANCE.rf.initialize(eventBus);
		// final Request<List<PlaceProxy>> findAllPlacesReq =
		// LoreKeeperState.INSTANCE.rf
		// .placeRequest().findAllPlaces();
		// findAllPlacesReq.fire(new Receiver<List<PlaceProxy>>() {
		// @Override
		// public void onSuccess(final List<PlaceProxy> response) {
		// wCreateHappening.setPlaces(response);
		// }
		//
		// @Override
		// public void onFailure(final ServerFailure error) {
		// GWT.log(error.getExceptionType());
		// GWT.log(error.getMessage());
		// GWT.log(error.getStackTraceString());
		// super.onFailure(error);
		// }
		// });
	}
}
