package ar.com.lorekeeper.client.happening;

import java.util.List;

import ar.com.lorekeeper.client.place.PlaceSelector;
import ar.com.lorekeeper.shared.bean.PlaceProxy;

import com.google.gwt.core.client.GWT;
import com.google.gwt.uibinder.client.UiBinder;
import com.google.gwt.uibinder.client.UiField;
import com.google.gwt.user.client.ui.Composite;
import com.google.gwt.user.client.ui.TextArea;
import com.google.gwt.user.client.ui.TextBox;
import com.google.gwt.user.client.ui.Widget;

public class CreateHappening extends Composite {

	interface CreateHappeningUiBinder extends UiBinder<Widget, CreateHappening> {
	}

	private static CreateHappeningUiBinder uiBinder = GWT
			.create(CreateHappeningUiBinder.class);

	@UiField
	TextBox txtWhatTitle;

	@UiField
	TextArea txtWhatDesc;

	@UiField
	PlaceSelector placeSelector;

	public CreateHappening() {
		initWidget(uiBinder.createAndBindUi(this));
	}

	public void setPlaces(final List<PlaceProxy> places) {
		placeSelector.setPlaces(places);
	}
}
