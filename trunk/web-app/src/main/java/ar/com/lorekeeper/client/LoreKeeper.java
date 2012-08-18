package ar.com.lorekeeper.client;

import java.util.List;

import ar.com.lorekeeper.client.place.PlacesTreeWidget;
import ar.com.lorekeeper.shared.bean.PlaceProxy;
import ar.com.lorekeeper.shared.service.LoreKeeperRequestFactory;

import com.google.gwt.core.client.EntryPoint;
import com.google.gwt.core.client.GWT;
import com.google.gwt.user.client.ui.RootPanel;
import com.google.web.bindery.requestfactory.shared.Receiver;
import com.google.web.bindery.requestfactory.shared.Request;

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

		rf = GWT.create(LoreKeeperRequestFactory.class);
		final Request<List<PlaceProxy>> findAllPlacesReq = rf.placeRequest()
				.findAllPlaces();
		findAllPlacesReq.fire(new Receiver<List<PlaceProxy>>() {
			@Override
			public void onSuccess(final List<PlaceProxy> response) {
				w.setPlaces(response);
			}
		});
	}
}
