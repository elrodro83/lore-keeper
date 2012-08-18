package ar.com.lorekeeper.server.dao;

import javax.annotation.PostConstruct;

import org.springframework.stereotype.Component;

import com.db4o.Db4oEmbedded;
import com.db4o.EmbeddedObjectContainer;
import com.db4o.ObjectContainer;
import com.db4o.config.EmbeddedConfiguration;
import com.db4o.config.EmbeddedConfigurationItem;

@Component
public class Db4oWrapper {

	private ObjectContainer db;

	@PostConstruct
	public void init() {
		EmbeddedConfiguration db4oConfig = Db4oEmbedded.newConfiguration();
		db4oConfig.addConfigurationItem(new EmbeddedConfigurationItem() {
			
			@Override
			public void prepare(EmbeddedConfiguration configuration) {
//				configuration.common().objectClass(Date.class).storeTransientFields(true);
			}
			
			@Override
			public void apply(EmbeddedObjectContainer db) {
			}
		});
		
		db = Db4oEmbedded.openFile(db4oConfig, "lore-keeper.yap");
	}
	
	public ObjectContainer getDb() {
		return db;
	}

}
