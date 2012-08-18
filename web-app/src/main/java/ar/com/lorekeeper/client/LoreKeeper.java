package ar.com.lorekeeper.client;

import java.util.List;

import ar.com.lorekeeper.client.place.PlacesTreeWidget;
import ar.com.lorekeeper.shared.bean.PlaceProxy;
import ar.com.lorekeeper.shared.service.LoreKeeperRequestFactory;

import com.google.gwt.core.client.EntryPoint;
import com.google.gwt.core.client.GWT;
import com.google.gwt.event.shared.EventBus;
import com.google.gwt.event.shared.SimpleEventBus;
import com.google.gwt.user.client.ui.RootPanel;
import com.google.web.bindery.requestfactory.shared.Receiver;
import com.google.web.bindery.requestfactory.shared.Request;
import com.google.web.bindery.requestfactory.shared.ServerFailure;

/**
 * Entry point classes define <code>onModuleLoad()</code>.
 */
public class LoreKeeper implements EntryPoint {

	private LoreKeeperRequestFactory rf;

	/**
	 * This is the entry point method.
	 */
	public void onModuleLoad() {
		final PlacesTreeWidget w = new PlacesTreeWidget();
		RootPanel.get("content").add(w);

		final EventBus eventBus = new SimpleEventBus();
		rf = GWT.create(LoreKeeperRequestFactory.class);
		rf.initialize(eventBus);
		final Request<List<PlaceProxy>> findAllPlacesReq = rf.placeRequest()
				.findAllPlaces();
		findAllPlacesReq.fire(new Receiver<List<PlaceProxy>>() {
			@Override
			public void onSuccess(final List<PlaceProxy> response) {
				w.setPlaces(response);
			}

			@Override
			public void onFailure(final ServerFailure error) {
				GWT.log(error.getExceptionType());
				GWT.log(error.getMessage());
				GWT.log(error.getStackTraceString());
				super.onFailure(error);
			}
		});
	}
}
