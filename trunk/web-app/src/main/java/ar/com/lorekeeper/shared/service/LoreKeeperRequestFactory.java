package ar.com.lorekeeper.shared.service;

import com.google.web.bindery.requestfactory.shared.RequestFactory;

public interface LoreKeeperRequestFactory extends RequestFactory {

	SearchRequest searchRequest();

	PlaceRequest placeRequest();

}
