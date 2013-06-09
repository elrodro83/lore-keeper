package ar.com.lorekeeper.client.place;

import java.util.List;

import ar.com.lorekeeper.client.LoreKeeperState;
import ar.com.lorekeeper.client.place.CreatePlace.CreatePlaceHandler;
import ar.com.lorekeeper.shared.bean.PlaceProxy;

import com.google.gwt.core.client.GWT;
import com.google.gwt.event.dom.client.ClickEvent;
import com.google.gwt.uibinder.client.UiBinder;
import com.google.gwt.uibinder.client.UiField;
import com.google.gwt.uibinder.client.UiHandler;
import com.google.gwt.user.client.ui.Button;
import com.google.gwt.user.client.ui.Composite;
import com.google.gwt.user.client.ui.ListBox;
import com.google.gwt.user.client.ui.Widget;

public class PlaceSelector extends Composite {

	interface PlaceSelectorUiBinder extends UiBinder<Widget, PlaceSelector> {
	}

	private static PlaceSelectorUiBinder uiBinder = GWT
			.create(PlaceSelectorUiBinder.class);

	@UiField
	ListBox cmbAllPlaces;
	@UiField
	Button btnNewChild;
	@UiField
	Button btnNew;

	private List<PlaceProxy> places;

	public PlaceSelector() {
		initWidget(uiBinder.createAndBindUi(this));
	}

	public void setPlaces(final List<PlaceProxy> places) {
		this.places = places;
		for (final PlaceProxy place : places) {
			cmbAllPlaces.addItem(place.getName());
		}
	}

	public PlaceProxy getSelectedPlace() {
		return places.get(cmbAllPlaces.getSelectedIndex());
	}

	public void setSelectedPlace(final PlaceProxy place) {
		cmbAllPlaces.setSelectedIndex(places.indexOf(place));
	}

	@UiHandler(value = "btnNewChild")
	public void onNewChildClick(final ClickEvent event) {
		final CreatePlace w = new CreatePlace();
		w.setParent(getSelectedPlace());
		showCreatePlace(w);
	}

	@UiHandler(value = "btnNew")
	public void onNewClick(final ClickEvent event) {
		showCreatePlace(new CreatePlace());
	}

	private void showCreatePlace(final CreatePlace w) {
		w.setHandler(new CreatePlaceHandler() {

			@Override
			public void onCreate(final PlaceProxy place) {
				places.add(place);
				cmbAllPlaces.addItem(place.getName());
				cmbAllPlaces.setSelectedIndex(places.indexOf(place));
				LoreKeeperState.INSTANCE.appPanel.remove(w);
				LoreKeeperState.INSTANCE.appPanel.showWidget(0);
			}

			@Override
			public void onCancel() {
				LoreKeeperState.INSTANCE.appPanel.remove(w);
				LoreKeeperState.INSTANCE.appPanel.showWidget(0);
			}
		});
		LoreKeeperState.INSTANCE.appPanel.add(w);
		LoreKeeperState.INSTANCE.appPanel.showWidget(1);
	}

}
